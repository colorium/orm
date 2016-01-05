<?php

namespace Colorium\Orm\Mapper;

class Native implements Source
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
     * Generate builder
     *
     * @param string $name
     * @return Native\Builder
     */
    public function builder($name)
    {
        $class = isset($this->mapping[$name])
            ? $this->mapping[$name]
            : null;

        return new Native\Builder($name, $this->pdo, $class);
    }


    /**
     * Generate query
     *
     * @param string $name
     * @return Native\Query
     */
    public function query($name)
    {
        $class = isset($this->mapping[$name])
            ? $this->mapping[$name]
            : null;

        return new Native\Query($name, $this->pdo, $class);
    }


    /**
     * Alias of query(name, class)
     *
     * @param $name
     * @param array $args
     * @return Native\Query
     */
    public function __call($name, array $args)
    {
        return $this->query($name, ...$args);
    }


    /**
     * Alias of query(name)
     *
     * @param $name
     * @return Native\Query
     */
    public function __get($name)
    {
        return $this->query($name);
    }


    /**
     * Execute raw query
     *
     * @param string $query
     * @param array $params
     * @param string $class
     * @return mixed
     */
    public function raw($query, array $params = [], $class = null)
    {
        // prepare statement & execute
        if($statement = $this->pdo->prepare($query) and $result = $statement->execute($params)) {

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