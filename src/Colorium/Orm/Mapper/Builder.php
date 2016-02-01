<?php

namespace Colorium\Orm\Mapper;

use Colorium\Orm\Contract\BuilderInterface;
use Colorium\Orm\SafePDO;
use Colorium\Orm\SQL;

class Builder extends SafePDO implements BuilderInterface
{

    /** @var Entity */
    protected $entity;

    /** @var string */
    protected $class;


    /**
     * Query constructor
     *
     * @param Entity $entity
     * @param \PDO $pdo
     */
    public function __construct(Entity $entity, \PDO $pdo)
    {
        parent::__construct($pdo);
        $this->entity = $entity;
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
            return $this->execute($sql);
        }
        catch(\PDOException $e) {
            return false;
        }
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
        return $this->execute($sql);
    }


    /**
     * Wipe entitys
     *
     * @return bool
     */
    public function wipe()
    {
        $sql = SQL::dropTable($this->entity->name);
        return $this->execute($sql);
    }


    /**
     * Clear entity data
     *
     * @return bool
     */
    public function clear()
    {
        $sql = SQL::truncateTable($this->entity->name);
        return $this->execute($sql);
    }

}