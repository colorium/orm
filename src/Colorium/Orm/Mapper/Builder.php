<?php

namespace Colorium\Orm\Mapper;

use Colorium\Orm\Contract\BuilderInterface;
use Colorium\Orm\SQL;

class Builder implements BuilderInterface
{

    /** @var Entity */
    protected $entity;

    /** @var string */
    protected $class;

    /** @var \PDO */
    protected $pdo;


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
     * Check if entity exists in source
     *
     * @return bool
     */
    public function exists()
    {
        try {
            $sql = SQL::tableExists($this->entity->name);
            $this->pdo->query($sql)->execute();
        }
        catch(\PDOException $e) {
            return false;
        }

        return true;
    }


    /**
     * Create entity
     *
     * @return bool
     */
    public function create()
    {
        $opts = [];
        foreach($this->entity->fields as $field) {
            $opts[$field->name] = get_object_vars($field);
        }

        $sql = SQL::createTable($this->entity->name, $opts);
        return $this->pdo->query($sql)->execute();
    }


    /**
     * Wipe entitys
     *
     * @return bool
     */
    public function wipe()
    {
        $sql = SQL::dropTable($this->entity->name);
        return $this->pdo->query($sql)->execute();
    }


    /**
     * Clear entity data
     *
     * @return bool
     */
    public function clear()
    {
        $sql = SQL::truncateTable($this->entity->name);
        return $this->pdo->query($sql)->execute();
    }

}