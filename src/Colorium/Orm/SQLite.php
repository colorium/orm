<?php

namespace Colorium\Orm;

class SQLite extends Native\Connector
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