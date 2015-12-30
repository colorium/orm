<?php

namespace Colorium\Orm;

abstract class Mapper
{

    /** @var Mapper\Connector */
    protected static $connector;


    /**
     * Connector accessor
     *
     * @param Mapper\Connector $connector
     * @return Mapper\Connector
     */
    public static function instance(Mapper\Connector $connector = null)
    {
        if($connector) {
            static::$connector = $connector;
        }
        elseif(!isset(static::$connector)) {
            throw new \LogicException('No connector instance stored');
        }

        return static::$connector;
    }


    /**
     * MySQL driver constructor
     *
     * @param string $dbname
     * @param array $settings
     * @return MySQL
     */
    public static function MySQL($dbname, array $settings = [])
    {
        $mysql = new MySQL($dbname, $settings);
        return static::instance($mysql);
    }


    /**
     * SQLite driver connector
     *
     * @param string $filename
     * @return MySQL
     */
    public static function SQLite($filename)
    {
        $sqlite = new SQLite($filename);
        return static::instance($sqlite);
    }


    /**
     * Generate query builder
     *
     * @param string $name
     * @param string $class
     * @return Mapper\Query
     */
    public static function query($name, $class = null)
    {
        return static::instance()->query($name, $class);
    }


    /**
     * Alias of query(name, class)
     *
     * @param string $name
     * @param array $args
     * @return Mapper\Query
     */
    public static function __callStatic($name, array $args)
    {
        return static::query($name, ...$args);
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
        return static::instance()->raw($sql, $params, $class);
    }


    /**
     * Fetch many result
     *
     * @param string $name
     * @param array $where
     * @param array $sort
     * @return object[]
     */
    public static function fetch($name, array $where = [], array $sort = [])
    {
        $query = static::query($name);
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
     * @param string $name
     * @param array $where
     * @return object
     */
    public static function one($name, array $where = [])
    {
        $query = static::query($name);
        foreach($where as $expression => $value) {
            $query->where($expression, $value);
        }

        return $query->one();
    }


    /**
     * Add record
     *
     * @param string $name
     * @param array $values
     * @return int
     */
    public static function add($name, array $values)
    {
        return static::query($name)->add($values);
    }


    /**
     * Edit record
     *
     * @param string $name
     * @param array $values
     * @param array $where
     * @return int
     */
    public static function edit($name, array $values, array $where = [])
    {
        $query = static::query($name);
        foreach($where as $expression => $value) {
            $query->where($expression, $value);
        }

        return $query->add($values);
    }


    /**
     * Delete record
     *
     * @param string $name
     * @param array $where
     * @return int
     */
    public static function drop($name, array $where = [])
    {
        $query = static::query($name);
        foreach($where as $expression => $value) {
            $query->where($expression, $value);
        }

        return $query->drop();
    }

}