<?php

namespace Colorium\Orm\Native;

use Colorium\Orm\Mapper;

class Connector implements Mapper\Connector
{

    /** @var \PDO */
    protected $pdo;

    /** @var array */
    protected $mapping = [];


    /**
     * Native PDO driver constructor
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Map entity name to class
     *
     * @param string $name
     * @param string $class
     * @return $this
     */
    public function map($name, $class)
    {
        $this->mapping[$name] = $class;

        return $this;
    }


    /**
     * Generate query builder
     *
     * @param string $name
     * @param string $class
     * @return Query
     */
    public function query($name, $class = null)
    {
        if($key = array_search($name, $this->mapping)) {
            $class = $name;
            $name = $key;
        }
        elseif(!$class and isset($this->mapping[$name])) {
            $class = $this->mapping[$name];
        }

        return new Query($name, $this->pdo, $class);
    }


    /**
     * Alias of query(name, class)
     *
     * @param $name
     * @param array $args
     * @return Query
     */
    public function __call($name, array $args)
    {
        return $this->query($name, ...$args);
    }


    /**
     * Alias of query(name)
     *
     * @param $name
     * @return Query
     */
    public function __get($name)
    {
        return $this->query($name);
    }


    /**
     * Execute raw query
     *
     * @param string $sql
     * @param array $params
     * @param string $class
     * @return mixed
     */
    public function raw($sql, array $params = [], $class = null)
    {
        // prepare statement & execute
        if($statement = $this->pdo->prepare($sql) and $result = $statement->execute($params)) {

            // collection
            if($statement->columnCount() > 0) {
                return $class
                    ? $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $class)
                    : $statement->fetchAll(\PDO::FETCH_OBJ);
            }

            // action
            return $statement->rowCount();
        }

        // error
        $error = $this->pdo->errorInfo();
        throw new \PDOException('[' . $error[0] . '] ' . $error[2], $error[0]);
    }

}