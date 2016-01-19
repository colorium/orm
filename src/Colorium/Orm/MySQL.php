<?php

namespace Colorium\Orm;

class MySQL extends Mapper
{

    /**
     * MySQL driver constructor
     *
     * @param array $settings
     * @param array $classmap
     */
    public function __construct($settings, array $classmap = [])
    {
        // localhost
        if(!is_array($settings)) {
            $settings = [
                'host'      => 'localhost',
                'username'  => 'root',
                'password'  => '',
                'dbname'    => $settings,
            ];
        }

        // create pdo instance
        $connector = 'mysql:host=' . $settings['host'] . ';dbname=' . $settings['dbname'];
        $pdo = new \PDO($connector, $settings['username'], $settings['password']);

        parent::__construct($pdo, $classmap);
    }

}