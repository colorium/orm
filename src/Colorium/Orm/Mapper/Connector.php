<?php

namespace Colorium\Orm\Mapper;

interface Connector
{

    /**
     * Map entity name to class
     *
     * @param string $name
     * @param string $class
     */
    public function map($name, $class);

    /**
     * Generate query builder
     *
     * @param string $name
     * @param string $class
     * @return Query
     */
    public function query($name, $class = null);

    /**
     * Execute raw query
     *
     * @param string $sql
     * @param array $params
     * @param string $class
     * @return mixed
     */
    public function raw($sql, array $params = [], $class = null);

}