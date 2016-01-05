<?php

namespace Colorium\Orm;

abstract class Mapper
{

    /** @var Mapper\Source */
    protected static $source;


    /**
     * Source accessor
     *
     * @param Mapper\Source $source
     * @return Mapper\Source
     */
    public static function source(Mapper\Source $source = null)
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
     * @param string $dbname
     * @param array $settings
     * @return MySQL
     */
    public static function MySQL($dbname, array $settings = [])
    {
        $mysql = new MySQL($dbname, $settings);
        return static::source($mysql);
    }


    /**
     * SQLite source
     *
     * @param string $filename
     * @return MySQL
     */
    public static function SQLite($filename)
    {
        $sqlite = new SQLite($filename);
        return static::source($sqlite);
    }


    /**
     * Generate query
     *
     * @param string $name
     * @return Mapper\Source\Query
     */
    public static function query($name)
    {
        return static::source()->query($name);
    }


    /**
     * Alias of query(name)
     *
     * @param string $name
     * @param array $args
     * @return Mapper\Source\Query
     */
    public static function __callStatic($name, array $args)
    {
        return static::query($name);
    }


    /**
     * Execute raw query
     *
     * @param string $query
     * @param array $params
     * @param string $class
     * @return mixed
     */
    public static function raw($query, array $params = [], $class = null)
    {
        return static::source()->raw($query, $params, $class);
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