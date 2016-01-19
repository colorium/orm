<?php

namespace Colorium\Orm;

class Mapper implements Source
{

    /** @var \PDO */
    protected $pdo;

    /** @var array */
    protected $classmap = [];


    /**
     * Native PDO driver constructor
     *
     * @param \PDO $pdo
     * @param array $classmap
     */
    public function __construct(\PDO $pdo, array $classmap = [])
    {
        $this->pdo = $pdo;
        $this->classmap = $classmap;
    }


    /**
     * Generate query
     *
     * @param string $entity
     * @return Mapper\Query
     */
    public function query($entity)
    {
        $class = $this->classOf($entity);
        return new Mapper\Query($entity, $this->pdo, $class);
    }


    /**
     * Generate builder
     *
     * @param string $entity
     * @return Mapper\Builder
     */
    public function builder($entity)
    {
        $class = $this->classOf($entity);
        return new Mapper\Builder($entity, $this->pdo, $class);
    }


    /**
     * Alias of query(name)
     *
     * @param $entity
     * @return Mapper\Query
     */
    public function __get($entity)
    {
        return $this->query($entity);
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


    /**
     * Get class related to entity
     * @param string $entity
     * @return string
     */
    protected function classOf($entity)
    {
        return isset($this->classmap[$entity])
            ? $this->classmap[$entity]
            : null;
    }

}