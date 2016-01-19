<?php

namespace Colorium\Orm;

interface Source
{

    /**
     * Generate entity query
     *
     * @param string $entity
     * @return Source\Query
     */
    public function query($entity);

    /**
     * Generate entity query
     *
     * @param string $entity
     * @return Source\Builder
     */
    public function builder($entity);

    /**
     * Execute raw query
     *
     * @param string $sql
     * @param array $values
     * @param string $class
     * @return mixed
     */
    public function raw($sql, array $values = [], $class = null);

}