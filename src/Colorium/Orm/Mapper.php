<?php

namespace Colorium\Orm;

class Mapper extends SafePDO implements Contract\SourceInterface
{

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
        parent::__construct($pdo);
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
     * @param array $values
     * @param string $class
     * @return mixed
     */
    public function raw($sql, array $values = [], $class = null)
    {
        return $this->execute($sql, $values, function(\PDOStatement $statement) use($class)
        {
            if($statement->columnCount() > 0) {
                return $class
                    ? $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $class)
                    : $statement->fetchAll(\PDO::FETCH_OBJ);
            }

            return $statement->rowCount();
        });
    }

}