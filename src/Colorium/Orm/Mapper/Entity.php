<?php

namespace Colorium\Orm\Mapper;

use Colorium\Runtime\Annotation;

class Entity
{

    /** @var string */
    public $name;

    /** @var string */
    public $class;

    /** @var Entity\Field[] */
    public $fields = [];


    /**
     * Define entity
     *
     * @param string $name
     * @param string $class
     */
    public function __construct($name, $class = null)
    {
        $this->name = $name;
        $this->class = $class;
    }


    /**
     * Define field
     *
     * @param string $field
     * @param string $type
     * @param bool $nullable
     * @param mixed $default
     * @param bool $primary
     * @return $this
     */
    public function set($field, $type, $nullable = true, $default = null, $primary = false)
    {
        $this->fields[$field] = new Entity\Field($field, $type, $nullable, $default, $primary);

        return $this;
    }


    /**
     * Generate entity from model class
     *
     * @param string $class
     * @return Entity
     */
    public static function of($class)
    {
        $reflector = new \ReflectionClass($class);

        $name = Annotation::ofClass($class, 'entity') ?: strtolower($reflector->getShortName());
        $entity = new static($name, $class);

        $defaults = $reflector->getDefaultProperties();
        foreach ($defaults as $property => $default) {
            $annotations = Annotation::ofProperty($class, $property);
            $type = !empty($annotations['var']) ? $annotations['var'] : 'string';
            $field = new Entity\Field($property, $type);
            $field->nullable = $default !== null;
            $field->default = $default;
            $field->primary = isset($annotations['id']) ?: ($property === 'id');

            $entity->fields[$property] = $field;
        }

        return $entity;
    }

}