<?php

namespace Colorium\Orm\Native;

use Colorium\Orm\Mapper;

class Query implements Mapper\Query
{

    /** @var string */
    protected $name;

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
     * @param string $name
     * @param \PDO $pdo
     * @param string $class
     */
    public function __construct($name, \PDO $pdo, $class = null)
    {
        $this->name = $name;
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
    public function where($expression, $value)
    {
        // parse last
        $split = explode(' ', $expression);
        $last = end($split);

        // case 1 : missing '= ?'
        if(preg_match('/^[a-zA-Z_0-9]+$/', $expression)) {
            $expression .= ' = ?';
        }

        // case 2 : missing '?'
        elseif(in_array($last, $this->operators)) {
            if(is_array($value)) {
                $placeholders = array_fill(0, count($value), '?');
                $expression .= ' (' . implode(', ', $placeholders) . ')';
            }
            else {
                $expression .= ' ?';
            }
        }

        $this->where[$expression] = $value;
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
        list($sql, $values) = $this->compiler->select($this->name, $fields, $this->where, $this->sort, $this->limit);

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
     * @param array $values
     * @return int
     */
    public function add(array $values)
    {
        list($sql, $values) = $this->compiler->insert($this->name, $values);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $result = $statement->execute($values)) {
            return $this->pdo->lastInsertId();
        }
        // error
        $error = $this->pdo->errorInfo();
        throw new \PDOException('[' . $error[0] . '] ' . $error[2], $error[0]);
    }


    /**
     * Edit record
     *
     * @param array $values
     * @return int
     */
    public function edit(array $values)
    {
        list($sql, $values) = $this->compiler->update($this->name, $values, $this->where);

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
        list($sql, $values) = $this->compiler->delete($this->name, $this->where);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $result = $statement->execute($values)) {
            return $statement->rowCount();
        }
        // error
        $error = $this->pdo->errorInfo();
        throw new \PDOException('[' . $error[0] . '] ' . $error[2], $error[0]);
    }

}