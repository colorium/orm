<?php

namespace Colorium\Orm\Mapper;

interface Query
{

    /**
     * Filter by conditions
     *
     * @param string $expression
     * @param mixed $value
     * @return $this
     */
    public function where($expression, $value);

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
     * @param array $values
     * @return int
     */
    public function add(array $values);

    /**
     * Edit record (UPDATE)
     *
     * @param array $values
     * @return int
     */
    public function edit(array $values);

    /**
     * Erase record (DROP)
     *
     * @return int
     */
    public function drop();

}