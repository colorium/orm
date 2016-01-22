<?php

namespace Colorium\Orm\Contract;

interface BuilderInterface
{

    /**
     * Check if entity exists
     *
     * @return bool
     */
    public function exists();

    /**
     * Create entity
     *
     * @return bool
     */
    public function create();

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