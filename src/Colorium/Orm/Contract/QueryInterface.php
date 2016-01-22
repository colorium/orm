<?php

namespace Colorium\Orm\Contract;

interface QueryInterface
{

    /**
     * Filter by conditions
     *
     * @param string $expression
     * @param mixed $value
     * @return $this
     */
    public function where($expression, $value = null);

    /**
     * Sort by field
     *
     * @param string $field
     * @param int $sort
     * @return $this
     */
    public function sort($field, $sort = SORT_ASC);

    /**
     * Limit results
     *
     * @param int $i
     * @param int $step
     * @return $this
     */
    public function limit($i, $step = 0);

    /**
     * Fetch many result (SELECT)
     *
     * @param string $fields
     * @return object[]
     */
    public function fetch(...$fields);

    /**
     * Fetch one result (SELECT)
     *
     * @param string $fields
     * @return object
     */
    public function one(...$fields);

    /**
     * Add record (INSERT)
     *
     * @param mixed $values
     * @return int
     */
    public function add($values);

    /**
     * Edit record (UPDATE)
     *
     * @param mixed $values
     * @return int
     */
    public function edit($values);

    /**
     * Erase record (DROP)
     *
     * @return int
     */
    public function drop();

}