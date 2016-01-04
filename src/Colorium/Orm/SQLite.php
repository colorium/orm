<?php

namespace Colorium\Orm;

class SQLite extends Mapper\Native
{

    /**
     * SQLite driver connector
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $pdo = new \PDO('sqlite:' . $filename);
        parent::__construct($pdo);
    }

}