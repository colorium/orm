<?php

namespace Colorium\Orm;

use Colorium\Orm\Contract\BuilderInterface;
use Colorium\Orm\Contract\QueryInterface;

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
        $id = Hub::add(static::class, $this);
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
        return Hub::edit(static::class, $this, $where);
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
        return Hub::drop(static::class, $where);
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
        return Hub::fetch(static::class, $where, $sort);
    }


    /**
     * Fetch one result
     *
     * @param array $where
     * @return object
     */
    public static function one(array $where = [])
    {
        return Hub::one(static::class, $where);
    }


    /**
     * Generate query
     *
     * @return QueryInterface
     */
    public static function query()
    {
        return Hub::query(static::class);
    }


    /**
     * Generate builder
     *
     * @return BuilderInterface
     */
    public static function builder()
    {
        return Hub::builder(static::class);
    }

}