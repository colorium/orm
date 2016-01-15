<?php

namespace Colorium\Orm;

trait Model
{

    /**
     * @id
     * @var int
     */
    public $id;


    /**
     * Add record
     *
     * @return int
     */
    public function add()
    {
        $values = get_object_vars($this);
        return Mapper::add(get_called_class(), $values);
    }


    /**
     * Edit record
     *
     * @return int
     */
    public function edit()
    {
        if(!$this->id) {
            return false;
        }

        $where = ['id' => $this->id];
        $values = get_object_vars($this);
        return Mapper::edit(get_called_class(), $values, $where);
    }


    /**
     * Save record
     *
     * @return int
     */
    public function save()
    {
        return $this->id ? $this->edit() : $this->add();
    }


    /**
     * Delete record
     *
     * @return int
     */
    public function drop()
    {
        if(!$this->id) {
            return false;
        }

        $where['id'] = $this->id;
        return Mapper::drop(get_called_class(), $where);
    }


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
     * Generate query
     *
     * @return Mapper\Source\Query
     */
    public static function query()
    {
        return Mapper::query(get_called_class());
    }


    /**
     * Generate builder
     *
     * @return Mapper\Source\Builder
     */
    public static function builder()
    {
        return Mapper::builder(get_called_class());
    }

}