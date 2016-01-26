<?php

namespace Colorium\Orm\Mapper;

use Colorium\Orm\Contract\QueryInterface;
use Colorium\Orm\SQL;

class Query implements QueryInterface
{

    /** @var Entity */
    protected $entity;

    /** @var \PDO */
    protected $pdo;

    /** @var array */
    protected $where = [];

    /** @var array */
    protected $simpleOperators = ['=', '>', '>=', '<', '<=', '<>', 'like', 'not like'];

    /** @var array */
    protected $complexOperators = ['in', 'not in'];

    /** @var array */
    protected $tupleOperators = ['between', 'not between'];

    /** @var array */
    protected $sort = [];

    /** @var string */
    protected $limit = [];


    /**
     * Query constructor
     *
     * @param Entity $entity
     * @param \PDO $pdo
     */
    public function __construct(Entity $entity, \PDO $pdo)
    {
        $this->entity = $entity;
        $this->pdo = $pdo;
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
        // shortcut : id
        if(is_int($expression) and !$value) {
            $expression = ['id' => $expression];
        }
        // force array
        elseif(!is_array($expression)) {
            $expression = [$expression => $value];
        }

        // parse each expression
        foreach($expression as $condition => $input) {

            // parse condition
            if(!preg_match('/^(?P<field>[a-zA-Z_0-9]+)( (?P<operator>.+))?$/', $condition, $extract)) {
                throw new \PDOException('Invalid expression "' . $condition . '"');
            }

            // clean condition
            $field = trim($extract['field']);
            $operator = strtolower(trim($extract['operator'], ' ?'));

            // implicit '=' or 'in'
            if(!$operator) {
                $operator = is_array($input)
                    ? reset($this->complexOperators)
                    : reset($this->simpleOperators);
            }

            // simple operators
            if(in_array($operator, $this->simpleOperators)) {
                if(is_array($input)) {
                    throw new \PDOException('Operator "' . $operator . '" needs only one input value, in "' . $condition . '"');
                }

                $condition = '`' . $field . '` ' . $operator . ' ?';
            }
            // complex operators
            elseif(in_array($operator, $this->complexOperators)) {
                if(!is_array($input) or empty($input)) {
                    throw new \PDOException('Operator "' . $operator . '" needs multiple input values, in "' . $condition . '"');
                }

                $placeholders = array_fill(0, count($input), '?');
                $condition = '`' . $field . '` ' . $operator . ' (' . implode(', ', $placeholders) . ')';
            }
            // tuple operators
            elseif(in_array($operator, $this->tupleOperators)) {
                if(!is_array($input) or count($input) != 2) {
                    throw new \PDOException('Operator "' . $operator . '" needs exactly two input value, in "' . $condition . '"');
                }

                $condition = '`' . $field . '` ' . $operator . ' ? and ?';
            }
            // unknown operator
            else {
                throw new \PDOException('Invalid operator in "' . $condition . '"');
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
     * @param int $offset
     * @return $this
     */
    public function limit($i, $offset = 0)
    {
        $this->limit = [$offset, $i];
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
        if(!$fields) {
            $fields = array_keys($this->entity->fields) ?: ['*'];
        }

        list($sql, $values) = SQL::select($this->entity->name, $fields, $this->where, $this->sort, $this->limit);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $statement->execute($values)) {
            return $this->entity->class
                ? $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $this->entity->class)
                : $statement->fetchAll(\PDO::FETCH_OBJ);
        }

        throw $this->error($statement);
    }


    /**
     * Fetch one result
     *
     * @param string $fields
     * @return object
     */
    public function one(...$fields)
    {
        if($this->limit) {
            $this->limit[1] = 1; // keep offset
        }
        else {
            $this->limit(1);
        }

        $items = $this->fetch(...$fields);
        return reset($items);
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

        list($sql, $values) = SQL::insert($this->entity->name, $values);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql)) {
            if($statement->execute($values)) {
                return $this->pdo->lastInsertId();
            }

            throw $this->error($sql, $statement);
        }

        throw $this->error($sql);
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

        list($sql, $values) = SQL::update($this->entity->name, $values, $this->where);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql)) {
            if($statement->execute($values)) {
                return $statement->rowCount();
            }

            throw $this->error($sql, $statement);
        }

        throw $this->error($sql);
    }


    /**
     * Erase record (DROP)
     *
     * @return int
     */
    public function drop()
    {
        list($sql, $values) = SQL::delete($this->entity->name, $this->where);

        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql)) {
            if($statement->execute($values)) {
                return $statement->rowCount();
            }

            throw $this->error($sql, $statement);
        }

        throw $this->error($sql);
    }


    /**
     * Generate PDO error
     *
     * @param string $sql
     * @param \PDOStatement $statement
     * @return \PDOException
     */
    protected function error($sql, \PDOStatement $statement = null)
    {
        $error = $this->pdo->errorInfo();
        if(!$error[1] and $statement) {
            $error = $statement->errorInfo();
        }

        $code = is_int($error[0]) ? $error[0] : null;
        return new \PDOException('[' . $error[0] . '] ' . $error[2] . ' in (' . $sql . ')', $code);
    }

}