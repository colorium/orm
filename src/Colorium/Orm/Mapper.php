<?php

namespace Colorium\Orm;

class Mapper implements Contract\SourceInterface
{

    /** @var \PDO */
    protected $pdo;

    /** @var Mapper\Entity[] */
    protected $entities = [];

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
        foreach($classmap as $entity => $class) {
            $this->entities[$entity] = Mapper\Entity::of($class);
            $this->classmap[$class] = $entity;
        }
    }


    /**
     * Get entity definition (from name of class)
     *
     * @param string $entity
     * @return Mapper\Entity
     */
    public function entity($entity)
    {
        if(isset($this->classmap[$entity])) {
            $entity = $this->classmap[$entity];
        }
        elseif(!isset($this->entities[$entity])) {
            $this->entities[$entity] = new Mapper\Entity($entity);
        }

        return $this->entities[$entity];
    }


    /**
     * Generate query
     *
     * @param string $entity
     * @return Mapper\Query
     */
    public function query($entity)
    {
        $entity = $this->entity($entity);
        return new Mapper\Query($entity, $this->pdo);
    }


    /**
     * Generate builder
     *
     * @param string $entity
     * @return Mapper\Builder
     */
    public function builder($entity)
    {
        $entity = $this->entity($entity);
        return new Mapper\Builder($entity, $this->pdo);
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
        if(!$error[1]){
            $error = $statement->errorInfo();
        }
        throw new \PDOException('[' . $error[0] . '] ' . $error[2], $error[0]);
    }

}