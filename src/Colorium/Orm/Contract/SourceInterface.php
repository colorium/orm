<?php

namespace Colorium\Orm\Contract;

interface SourceInterface
{

    /**
     * Generate entity query
     *
     * @param string $entity
     * @return QueryInterface
     */
    public function query($entity);

    /**
     * Generate entity query
     *
     * @param string $entity
     * @return BuilderInterface
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