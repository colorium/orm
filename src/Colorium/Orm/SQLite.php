<?php

namespace Colorium\Orm;

class SQLite extends Mapper
{

    /**
     * SQLite driver connector
     *
     * @param string $filename
     * @param array $classmap
     */
    public function __construct($filename, array $classmap = [])
    {
        $pdo = new \PDO('sqlite:' . $filename);
        parent::__construct($pdo, $classmap);
    }


    /**
     * Generate builder
     *
     * @param string $entity
     * @return SQLite\Builder
     */
    public function builder($entity)
    {
        $class = $this->classOf($entity);
        return new SQLite\Builder($entity, $this->pdo, $class);
    }

}