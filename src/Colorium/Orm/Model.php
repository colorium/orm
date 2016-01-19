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
        $id = Hub::add(get_called_class(), $this);
        if($id) {
            $this->id = $id;
            return $id;
        }

        return false;
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
        return Hub::edit(get_called_class(), $this, $where);
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
        return Hub::drop(get_called_class(), $where);
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
        return Hub::fetch(get_called_class(), $where, $sort);
    }


    /**
     * Fetch one result
     *
     * @param array $where
     * @return object
     */
    public static function one(array $where = [])
    {
        return Hub::one(get_called_class(), $where);
    }


    /**
     * Generate query
     *
     * @return Mapper\Query
     */
    public static function query()
    {
        return Hub::query(get_called_class());
    }


    /**
     * Generate builder
     *
     * @return Mapper\Builder
     */
    public static function builder()
    {
        return Hub::builder(get_called_class());
    }

}