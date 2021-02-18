<?php
class DbManager {
    private $connection = null;
    private $needsCommit = false;
    public $result = null;
    public $transactionBegun = false;
    
    public function __construct() {
        $connection = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_errno()) {
            throw new Exception('Connection error code '.mysqli_connect_errno().'. '.mysqli_connect_error());
        } else {
            $this->connection = $connection;
        }
    }

    public function __destruct() {
        if (!is_null($this->connection)) {
            if ($this->transactionBegun && $this->needsCommit) {
                $this->connection->commit();
            }
            $this->connection->close();
        }
    }
    
    public function beginTransaction($readOnly=false) {
        if ($this->transactionBegun) {
            throw new Exception('Transaction already begun.');
        } else {
            if (version_compare(phpversion(), '5.5.0', '>=')) {
                $flag = $readOnly ? MYSQLI_TRANS_START_READ_ONLY : MYSQLI_TRANS_START_READ_WRITE;
                $this->connection->begin_transaction($flag);
            } else {
                $this->connection->autocommit(false);
            }
            $this->transactionBegun = true;
            $this->needsCommit = $readOnly ? false : true;
        }
    }

    public function query($query, array $params=array(), $paramTypes='') {
        if (empty($params) || !$paramTypes) {
            $this->result = $this->connection->query($query);
            if ($this->result===false) {
                throw new Exception('Query error in \''.$query.'\'. '.$this->connection->error.'.');
            }
        } else {
            $bindings = array();
            $bindings[] = & $paramTypes;
            $paramsLen = count($params);
            for ($i=0; $i<$paramsLen; $i++) {
                $bindings[$i+1] = & $params[$i];
            }
            $statement = $this->connection->prepare($query);
            if ($statement===false) {
                throw new Exception('Statement error in \''.$query.'\'. '.$this->connection->error.'.');
            } else {
                call_user_func_array(array($statement, 'bind_param'), $bindings);
                if (!$statement->execute()) {
                    throw new Exception('Execution error in \''.$query.'\'. '.$this->connection->error.'.');
                }
                $this->result = $statement->get_result();
                $statement->close();
            }
        }
    }
    
    public function rollbackTransaction() {
        if (!is_null($this->connection)) {
            $this->connection->rollback();
            $this->transactionBegun = false;
            $this->needsCommit = false;
        }
    }
}
