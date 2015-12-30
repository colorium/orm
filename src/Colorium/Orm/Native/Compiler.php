<?php

namespace Colorium\Orm\Native;


class Compiler
{

    /**
     * Compile SELECT
     *
     * @param string $table
     * @param array $fields
     * @param array $where
     * @param array $sort
     * @param string $limit
     * @return array
     */
    public function select($table, array $fields, array $where = [], array $sort = [], $limit = null)
    {
        $sql = $values = [];
        $sql[] = 'SELECT ' . implode(', ', $fields);
        $sql[] = 'FROM `' . $table . '`';

        if($where) {
            $items = [];
            foreach($where as $exp => $data) {
                $items[] = $exp;
                if(is_array($data)) {
                    $values = array_merge($values, $data);
                    continue;
                }
                $values[] = $data;
            }
            $sql[] = 'WHERE ' . implode(' AND ', $items);
        }

        if($sort) {
            $items = [];
            foreach($sort as $field => $direction) {
                $items[] = '`' . $field . '` ' . ($direction == SORT_DESC ? 'DESC' : 'ASC');
            }
            $sql[] = 'ORDER BY ' .implode(', ', $items);
        }

        if($limit) {
            $sql[] = 'LIMIT ' . $limit;
        }

        $sql = implode("\n", $sql);
        return [$sql, $values];
    }


    /**
     * Compile INSERT INTO
     *
     * @param string $name
     * @param array $set
     * @return array
     */
    public function insert($name, array $set)
    {
        $sql = $values = $fields = $holders = [];
        $sql[] = 'INSERT INTO `' . $name . '`';

        foreach($set as $field => $value) {
            $fields[] = '`' . $field . '`';
            $holders[] = '?';
            $values[] = $value;
        }

        $sql[] = '(' . implode(', ', $fields) . ')';
        $sql[] = 'VALUES (' . implode(', ', $holders) . ')';

        $sql = implode("\n", $sql);
        return [$sql, $values];
    }


    /**
     * Compile UPDATE
     *
     * @param string $name
     * @param array $set
     * @param array $where
     * @return array
     */
    public function update($name, array $set, array $where = [])
    {
        $sql = $values = $fields = [];
        $sql[] = 'UPDATE `' . $name . '`';

        foreach($set as $field => $value) {
            $fields[] = '`' . $field . '` = ?';
            $values[] = $value;
        }

        $sql[] = 'SET ' . implode(', ', $fields);

        if($where) {
            $where = [];
            foreach($where as $exp => $data) {
                $where[] = $exp;
                if(is_array($data)) {
                    $values = array_merge($values, $data);
                    continue;
                }
                $values[] = $data;
            }
            $sql[] = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = implode("\n", $sql);
        return [$sql, $values];
    }


    /**
     * Compile DELETE
     *
     * @param string $name
     * @param array $where
     * @return array
     */
    public function delete($name, array $where = [])
    {
        $sql = $values = [];
        $sql[] = 'DELETE FROM `' . $name . '`';

        if($where) {
            $where = [];
            foreach($where as $exp => $data) {
                $where[] = $exp;
                if(is_array($data)) {
                    $values = array_merge($values, $data);
                    continue;
                }
                $values[] = $data;
            }
            $sql[] = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = implode("\n", $sql);
        return [$sql, $values];
    }

}