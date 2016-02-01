<?php

namespace Colorium\Orm;

class SafePDO
{

    /** @var \PDO */
    protected $pdo;


    /**
     * Init with PDO instance
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Get inner pdo instance
     *
     * @return \PDO
     */
    public function pdo()
    {
        return $this->pdo;
    }


    /**
     * Safe SQL execution
     *
     * @param string $sql
     * @param array $values
     * @param \Closure $callback
     * @return \PDOStatement
     */
    protected function execute($sql, array $values = [], \Closure $callback = null)
    {
        if($statement = $this->pdo->prepare($sql)) {
            if($statement->execute($values)) {

                // format output
                $return = $callback ? $callback($statement) : true;

                // close statement connection
                $statement->closeCursor();
                unset($statement);

                return $return;
            }

            throw $this->error($sql, $statement);
        }

        throw $this->error($sql);
    }


    /**
     * Generate PDO error
     *
     * @param string $sql
     * @param \PDOStatement $statement
     * @return \PDOException
     */
    protected function error($sql, \PDOStatement $statement = null)
    {
        $error = $this->pdo->errorInfo();
        if(!$error[1] and $statement) {
            $error = $statement->errorInfo();
            $statement->closeCursor();
            unset($statement);
        }

        $code = is_int($error[0]) ? $error[0] : null;
        return new \PDOException('[' . $error[0] . '] ' . $error[2] . ', in query (' . $sql . ')', $code);
    }

}