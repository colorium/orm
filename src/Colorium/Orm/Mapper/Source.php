<?php

namespace Colorium\Orm\Mapper;

interface Source
{

    /**
     * Map entity class
     *
     * @param string $name
     * @param string $class
     * @return
     */
    public function map($name, $class);

    /**
     * Generate builder
     *
     * @param string $name
     * @return Source\Builder
     */
    public function builder($name);

    /**
     * Generate query
     *
     * @param string $name
     * @return Source\Query
     */
    public function query($name);

    /**
     * Execute raw query
     *
     * @param string $query
     * @param array $params
     * @param string $class
     * @return mixed
     */
    public function raw($query, array $params = [], $class = null);

}