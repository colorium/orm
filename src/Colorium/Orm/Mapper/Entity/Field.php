<?php

namespace Colorium\Orm\Mapper\Entity;

class Field
{

    /** @var string */
    public $name;

    /** @var string */
    public $type;

    /** @var bool */
    public $nullable = true;

    /** @var mixed */
    public $default = null;

    /** @var bool */
    public $primary = false;

    /** @var array */
    protected $aliases = [
        'string'            => 'VARCHAR(255)',
        'string password'   => 'VARCHAR(255)',
        'string email'      => 'VARCHAR(255)',
        'string text'       => 'TEXT',
        'string date'       => 'DATE',
        'string datetime'   => 'DATETIME',
        'int'               => 'INTEGER',
        'bool'              => 'BOOLEAN',
    ];


    /**
     * Define field
     *
     * @param string $name
     * @param string $type
     * @param bool $nullable
     * @param mixed $default
     * @param bool $primary
     */
    public function __construct($name, $type, $nullable = true, $default = null, $primary = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->default = $default;
        $this->primary = $primary;

        if(isset($this->aliases[$type])) {
            $this->type = $this->aliases[$type];
        }
    }

}