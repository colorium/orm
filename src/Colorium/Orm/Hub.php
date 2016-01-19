<?php

namespace Colorium\Orm;

abstract class Hub
{

    /** @var Source */
    protected static $source;


    /**
     * Source accessor
     *
     * @param Source $source
     * @return Source
     */
    public static function source(Source $source = null)
    {
        if($source) {
            static::$source = $source;
        }
        elseif(!isset(static::$source)) {
            throw new \LogicException('No source instance stored');
        }

        return static::$source;
    }


    /**
     * MySQL source
     *
     * @param string $settings
     * @param array $classmap
     * @return MySQL
     */
    public static function MySQL($settings, array $classmap = [])
    {
        $mysql = new MySQL($settings, $classmap);
        return static::source($mysql);
    }


    /**
     * SQLite source
     *
     * @param string $filename
     * @param array $classmap
     * @return MySQL
     */
    public static function SQLite($filename, array $classmap = [])
    {
        $sqlite = new SQLite($filename, $classmap);
        return static::source($sqlite);
    }


    /**
     * Generate query
     *
     * @param string $entity
     * @return Mapper\Query
     */
    public static function query($entity)
    {
        return static::source()->query($entity);
    }


    /**
     * Generate builder
     *
     * @param string $entity
     * @return Mapper\Builder
     */
    public static function builder($entity)
    {
        return static::source()->builder($entity);
    }


    /**
     * Alias of query(name)
     *
     * @param string $entity
     * @param array $args
     * @return Mapper\Query
     */
    public static function __callStatic($entity, array $args)
    {
        return static::query($entity);
    }


    /**
     * Execute raw query
     *
     * @param string $sql
     * @param array $params
     * @param string $class
     * @return mixed
     */
    public static function raw($sql, array $params = [], $class = null)
    {
        return static::source()->raw($sql, $params, $class);
    }


    /**
     * Fetch many result
     *
     * @param string $entity
     * @param array $where
     * @param array $sort
     * @return object[]
     */
    public static function fetch($entity, array $where = [], array $sort = [])
    {
        $query = static::query($entity);
        foreach($where as $expression => $value) {
            $query->where($expression, $value);
        }
        foreach($sort as $field => $direction) {
            $query->sort($field, $direction);
        }

        return $query->fetch();
    }


    /**
     * Fetch one result
     *
     * @param string $entity
     * @param array $where
     * @return object
     */
    public static function one($entity, array $where = [])
    {
        $query = static::query($entity);
        foreach($where as $expression => $value) {
            $query->where($expression, $value);
        }

        return $query->one();
    }


    /**
     * Add record
     *
     * @param string $entity
     * @param mixed $values
     * @return int
     */
    public static function add($entity, $values)
    {
        return static::query($entity)->add($values);
    }


    /**
     * Edit record
     *
     * @param string $entity
     * @param mixed $values
     * @param array $where
     * @return int
     */
    public static function edit($entity, $values, array $where = [])
    {
        $query = static::query($entity);
        foreach($where as $expression => $value) {
            $query->where($expression, $value);
        }

        return $query->add($values);
    }


    /**
     * Delete record
     *
     * @param string $entity
     * @param array $where
     * @return int
     */
    public static function drop($entity, array $where = [])
    {
        $query = static::query($entity);
        foreach($where as $expression => $value) {
            $query->where($expression, $value);
        }

        return $query->drop();
    }

}