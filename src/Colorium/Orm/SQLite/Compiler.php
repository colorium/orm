<?php

namespace Colorium\Orm\SQLite;

use Colorium\Orm\Mapper;

class Compiler extends Mapper\Compiler
{


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
                $sql .= ' PRIMARY KEY AUTOINCREMENT';
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
     * Compile TRUNCATE TABLE
     *
     * @param string $table
     * @return bool
     */
    public function truncateTable($table)
    {
        return 'DELETE FROM `' . $table . '`';
    }

}