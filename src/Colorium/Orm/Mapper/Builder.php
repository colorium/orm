<?php

namespace Colorium\Orm\Mapper;

use Colorium\Orm\Source;
use Colorium\Runtime\Annotation;

class Builder implements Source\Builder
{

    /** @var string */
    protected $entity;

    /** @var string */
    protected $class;

    /** @var \PDO */
    protected $pdo;

    /** @var Compiler */
    protected $compiler;

    /** @var array */
    protected $types = [
        'string'            => 'VARCHAR(255)',
        'string email'      => 'VARCHAR(255)',
        'string text'       => 'TEXT',
        'string date'       => 'DATE',
        'string datetime'   => 'DATETIME',
        'int'               => 'INTEGER',
        'bool'              => 'BOOLEAN',
    ];


    /**
     * Query constructor
     *
     * @param string $entity
     * @param \PDO $pdo
     * @param string $class
     */
    public function __construct($entity, \PDO $pdo, $class = null)
    {
        $this->entity = $entity;
        $this->pdo = $pdo;
        $this->class = $class;
        $this->compiler = new Compiler;
    }


    /**
     * Check if entity exists in source
     *
     * @return bool
     */
    public function exists()
    {
        try {
            $sql = $this->compiler->tableExists($this->entity);
            $this->pdo->query($sql);
        } catch(\PDOException $e) {
            return false;
        }

        return true;
    }


    /**
     * Create entity
     *
     * @param array $specs
     * @return bool
     */
    public function create(array $specs = [])
    {
        // read class specs
        if(!$specs and $this->class) {
            $reflector = new \ReflectionClass($this->class);
            $defaults = $reflector->getDefaultProperties();
            foreach ($defaults as $property => $default) {
                $annotations = Annotation::ofProperty($this->class, $property);
                $specs[$property] = [
                    'type' => !empty($annotations['var']) ? $annotations['var'] : 'string',
                    'nullable' => $default !== null,
                    'default' => $default,
                    'primary' => isset($annotations['id']) ?: ($property === 'id')
                ];
            }
        }
        // parse custom specs
        else {
            foreach($specs as $field => &$opts) {
                $opts += [
                    'type' => 'string',
                    'nullable' => true,
                    'default' => null,
                    'primary' => ($field === 'i')
                ];
                if(isset($this->types[$opts['type']])) {
                    $opts['type'] = $this->types[$opts['type']];
                }
            }
        }

        $sql = $this->compiler->createTable($this->entity, $specs);
        return $this->pdo->query($sql);
    }


    /**
     * Wipe entitys
     *
     * @return bool
     */
    public function wipe()
    {
        $sql = $this->compiler->dropTable($this->entity);
        return $this->pdo->query($sql);
    }


    /**
     * Clear entity data
     *
     * @return bool
     */
    public function clear()
    {
        $sql = $this->compiler->truncateTable($this->entity);
        return $this->pdo->query($sql);
    }

}