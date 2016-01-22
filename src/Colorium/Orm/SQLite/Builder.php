<?php

namespace Colorium\Orm\SQLite;

use Colorium\Orm\Mapper;
use Colorium\Runtime\Annotation;

class Builder extends Mapper\Builder
{

    /**
     * Create entity
     *
     * @return bool
     */
    public function create()
    {
        $opts = [];
        foreach($this->entity->fields as $field) {
            $opts[$field->name] = get_object_vars($field);
        }

        $sql = SQL::createTable($this->entity->name, $opts);
        return $this->pdo->query($sql)->execute();
    }


    /**
     * Clear entity data
     *
     * @return bool
     */
    public function clear()
    {
        $sql = SQL::truncateTable($this->entity->name);
        return $this->pdo->query($sql)->execute();
    }

}