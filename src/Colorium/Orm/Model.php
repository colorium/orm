<?php

namespace Colorium\Orm;

trait Model
{

    /**
     * Fetch many result
     *
     * @param array $where
     * @param array $sort
     * @return object[]
     */
    public static function fetch(array $where = [], array $sort = [])
    {
        return Mapper::fetch(get_called_class(), $where, $sort);
    }


    /**
     * Fetch one result
     *
     * @param array $where
     * @return object
     */
    public static function one(array $where = [])
    {
        return Mapper::one(get_called_class(), $where);
    }


    /**
     * Add record
     *
     * @param array $values
     * @return int
     */
    public static function add(array $values)
    {
        return Mapper::add(get_called_class(), $values);
    }


    /**
     * Edit record
     *
     * @param array $values
     * @param array $where
     * @return int
     */
    public static function edit(array $values, array $where = [])
    {
        return Mapper::edit(get_called_class(), $values, $where);
    }


    /**
     * Delete record
     *
     * @param array $where
     * @return int
     */
    public static function drop(array $where = [])
    {
        return Mapper::drop(get_called_class(), $where);
    }


    /**
     * Generate query builder
     *
     * @return Mapper\Query
     */
    public static function query()
    {
        return Mapper::query(get_called_class());
    }

}