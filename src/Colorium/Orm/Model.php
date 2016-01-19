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
        $id = Hub::add(static::entity(), $this);
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
        return Hub::edit(static::entity(), $this, $where);
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
        return Hub::drop(static::entity(), $where);
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
        return Hub::fetch(static::entity(), $where, $sort);
    }


    /**
     * Fetch one result
     *
     * @param array $where
     * @return object
     */
    public static function one(array $where = [])
    {
        return Hub::one(static::entity(), $where);
    }


    /**
     * Generate query
     *
     * @return Mapper\Query
     */
    public static function query()
    {
        return Hub::query(static::entity());
    }


    /**
     * Generate builder
     *
     * @return Mapper\Builder
     */
    public static function builder()
    {
        return Hub::builder(static::entity());
    }


    /**
     * Get self entity name
     *
     * @return string
     */
    public static function entity()
    {
        static $entity;
        if(!$entity) {
            $class = (new \ReflectionClass(static::class))->getShortName();
            $entity = strtolower($class);
        }

        return $entity;
    }

}