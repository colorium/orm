<?php

namespace Colorium\Orm\Mapper\Native;

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
     * @param string $table
     * @param array $set
     * @return array
     */
    public function insert($table, array $set)
    {
        $sql = $values = $fields = $holders = [];
        $sql[] = 'INSERT INTO `' . $table . '`';

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
     * @param string $table
     * @param array $set
     * @param array $where
     * @return array
     */
    public function update($table, array $set, array $where = [])
    {
        $sql = $values = $fields = [];
        $sql[] = 'UPDATE `' . $table . '`';

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
     * @param string $table
     * @param array $where
     * @return array
     */
    public function delete($table, array $where = [])
    {
        $sql = $values = [];
        $sql[] = 'DELETE FROM `' . $table . '`';

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
     * Compile TABLE EXISTS
     *
     * @param string $table
     * @return bool
     */
    public function tableExists($table)
    {
        return 'SELECT 1 FROM `' . $table . '`';
    }


    /**
     * Compile CREATE TABLE
     *
     * @param string $table
     * @param array $specs
     * @return bool
     */
    public function createTable($table, array $specs)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (';
        foreach($specs as $field => $opts) {
            $sql .= "\n" .  '`' . $field . '` ' . $opts['type'];
            if($opts['primary'] == true) {
                $opts['nullable'] = false;
                $opts['default'] = null;
                $sql .= ' PRIMARY KEY AUTO_INCREMENT';
            }
            if(!$opts['nullable']) {
                $sql .= ' NOT NULL';
            }
            if($opts['default']) {
                $sql .= ' DEFAULT ' . $opts['default'];
            }
            $sql .= ',';
        }

        $sql = trim($sql, ',') . "\n" . ');';
        return $sql;
    }


    /**
     * Compile DROP TABLE
     *
     * @param string $table
     * @return bool
     */
    public function dropTable($table)
    {
        return 'DROP TABLE IF EXISTS `' . $table . '`';
    }


    /**
     * Compile TRUNCATE TABLE
     *
     * @param string $table
     * @return bool
     */
    public function truncateTable($table)
    {
        return 'TRUNCATE TABLE `' . $table . '`';
    }

}