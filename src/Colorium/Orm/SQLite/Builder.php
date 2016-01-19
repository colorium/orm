<?php

namespace Colorium\Orm\SQLite;

use Colorium\Orm\Mapper;

class Builder extends Mapper\Builder
{

    /**
     * Builder constructor
     *
     * @param string $name
     * @param \PDO $pdo
     * @param string $class
     */
    public function __construct($name, \PDO $pdo, $class = null)
    {
        parent::__construct($name, $pdo, $class);
        $this->compiler = new Compiler;
    }

}