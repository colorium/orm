<?php

namespace Colorium\Orm\Mapper\Source;

interface Builder
{

    /**
     * Check if entoty exists in source
     *
     * @return bool
     */
    public function exists();

    /**
     * Create entity
     *
     * @param array $specs
     * @return bool
     */
    public function create(array $specs = []);

    /**
     * Wipe entity
     *
     * @return bool
     */
    public function wipe();

    /**
     * Clear entity data
     *
     * @return bool
     */
    public function clear();

}