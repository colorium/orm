<?php

namespace Colorium\Orm\Mapper;

use Colorium\Orm\Source;

class Query implements Source\Query
{

    /** @var string */
    protected $entity;

    /** @var string */
    protected $class;

    /** @var \PDO */
    protected $pdo;

    /** @var Compiler */
    protected $compiler;

    /** @var array */
    protected $where = [];

    /** @var array */
    protected $operators = ['>', '>=', '<', '<=', '=', 'is', 'not', 'in', 'exists'];

    /** @var array */
    protected $sort = [];

    /** @var string */
    protected $limit;


    /**
     * Query constructor
     *
     * @param string $entity
     * @param \PDO $pdo
     * @param string $class
     */
    public function __construct($entity, \PDO $pdo, $class = null)
    {
        $this->entity = $entity;
        $this->pdo = $pdo;
        $this->class = $class;
        $this->compiler = new Compiler;
    }


    /**
     * Filter by conditions
     *
     * @param string $expression
     * @param mixed $value
     * @return $this
     */
    public function where($expression, $value = null)
    {
        if(is_int($expression)) {
            $expression = ['id' => $expression];
        }
        elseif(!is_array($expression)) {
            $expression = [$expression => $value];
        }

        foreach($expression as $condition => $input) {

            // parse last
            $split = explode(' ', $condition);
            $last = end($split);

            // case 1 : missing '= ?'
            if(preg_match('/^[a-zA-Z_0-9]+$/', $condition)) {
                $condition .= ' = ?';
            }

            // case 2 : missing '?'
            elseif(in_array($last, $this->operators)) {
                if(is_array($input)) {
                    $placeholders = array_fill(0, count($input), '?');
                    $condition .= ' (' . implode(', ', $placeholders) . ')';
                }
                else {
                    $condition .= ' ?';
                }
            }

            $this->where[$condition] = $input;
        }

        return $this;
    }


    /**
     * Sort by field
     *
     * @param string $field
     * @param int $sort
     * @return $this
     */
    public function sort($field, $sort = SORT_ASC)
    {
        $this->sort[$field] = $sort;
        return $this;
    }


    /**
     * Limit results
     *
     * @param int $i
     * @param int $step
     * @return $this
     */
    public function limit($i, $step = 0)
    {
        $this->limit = $i;
        if($step) {
            $this->limit .= ', ' . $step;
        }

        return $this;
    }


    /**
     * Fetch many result
     *
     * @param string $fields
     * @return object[]
     */
    public function fetch(...$fields)
    {
        $fields = $fields ?: ['*'];
        list($sql, $values) = $this->compiler->select($this->entity, $fields, $this->where, $this->sort, $this->limit);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $result = $statement->execute($values)) {
            return $this->class
                ? $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->class)
                : $statement->fetchAll(\PDO::FETCH_OBJ);
        }
        // error
        $error = $this->pdo->errorInfo();
        throw new \PDOException('[' . $error[0] . '] ' . $error[2], $error[0]);
    }


    /**
     * Fetch one result
     *
     * @param string $fields
     * @return object
     */
    public function one(...$fields)
    {
        $items = $this->limit(1)->fetch(...$fields);
        return current($items);
    }


    /**
     * Add record
     *
     * @param mixed $values
     * @return int
     */
    public function add($values)
    {
        if(is_object($values)) {
            $values = get_object_vars($values);
        }
        elseif(!is_array($values)) {
            $values = (array)$values;
        }

        list($sql, $values) = $this->compiler->insert($this->entity, $values);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $result = $statement->execute($values)) {
            return $this->pdo->lastInsertId();
        }

        // error
        $error = $this->pdo->errorInfo();
        if(!$error[1]) {
            $error = $statement->errorInfo();
        }
        throw new \PDOException('[' . $error[0] . '] ' . ucfirst($error[2]), (int)$error[0]);
    }


    /**
     * Edit record
     *
     * @param mixed $values
     * @return int
     */
    public function edit($values)
    {
        if(is_object($values)) {
            $values = get_object_vars($values);
        }
        elseif(!is_array($values)) {
            $values = (array)$values;
        }

        list($sql, $values) = $this->compiler->update($this->entity, $values, $this->where);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $result = $statement->execute($values)) {
            return $statement->rowCount();
        }
        // error
        $error = $this->pdo->errorInfo();
        throw new \PDOException('[' . $error[0] . '] ' . $error[2], $error[0]);
    }


    /**
     * Erase record (DROP)
     *
     * @return int
     */
    public function drop()
    {
        list($sql, $values) = $this->compiler->delete($this->entity, $this->where);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $result = $statement->execute($values)) {
            return $statement->rowCount();
        }
        // error
        $error = $this->pdo->errorInfo();
        throw new \PDOException('[' . $error[0] . '] ' . $error[2], $error[0]);
    }

}