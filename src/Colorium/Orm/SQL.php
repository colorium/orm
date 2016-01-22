<?php

namespace Colorium\Orm;

abstract class SQL
{

    /**
     * Compile SELECT
     *
     * @param string $table
     * @param array $fields
     * @param array $where
     * @param array $sort
     * @param array $limit
     * @return array
     */
    public static function select($table, array $fields, array $where = [], array $sort = [], array $limit = [])
    {
        $sql = $values = [];
        $sql[] = 'SELECT ' . implode(', ', $fields);
        $sql[] = 'FROM ' . static::e($table);

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
                $items[] = static::e($field) . ' ' . ($direction == SORT_DESC ? 'DESC' : 'ASC');
            }
            $sql[] = 'ORDER BY ' .implode(', ', $items);
        }

        if($limit) {
            $sql[] = 'LIMIT ' . $limit[0] . ', ' . $limit[1];
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
    public static function insert($table, array $set)
    {
        $sql = $values = $fields = $holders = [];
        $sql[] = 'INSERT INTO ' . static::e($table);

        foreach($set as $field => $value) {
            $fields[] = static::e($field);
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
    public static function update($table, array $set, array $where = [])
    {
        $sql = $values = $fields = [];
        $sql[] = 'UPDATE ' . static::e($table);

        foreach($set as $field => $value) {
            $fields[] = static::e($field) . ' = ?';
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
    public static function delete($table, array $where = [])
    {
        $sql = $values = [];
        $sql[] = 'DELETE FROM ' . static::e($table);

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
    public static function tableExists($table)
    {
        return 'SELECT 1 FROM ' . static::e($table);
    }


    /**
     * Compile CREATE TABLE
     *
     * @param string $table
     * @param array $specs
     * @return bool
     */
    public static function createTable($table, array $specs)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . static::e($table) . ' (';
        foreach($specs as $field => $opts) {
            $sql .= "\n" .  static::e($field) . ' ' . $opts['type'];
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
    public static function dropTable($table)
    {
        return 'DROP TABLE IF EXISTS ' . static::e($table);
    }


    /**
     * Compile TRUNCATE TABLE
     *
     * @param string $table
     * @return bool
     */
    public static function truncateTable($table)
    {
        return 'TRUNCATE TABLE ' . static::e($table);
    }


    /**
     * Protect string
     *
     * @param $field
     * @return string
     */
    protected static function e($field)
    {
        return '`' . trim($field, '`') . '`';
    }

}