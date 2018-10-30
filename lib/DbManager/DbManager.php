<?php
require('config.php');

class DbManager {
    private $connection;
    private $queryRes;
    
    public function __construct() {
        $connection = new mysqli(SERVER, USER, PASSWORD, DATABASE);
        if ($connection->connect_error) {
            die('Connection ERROR ' . $connection->connect_errno . '!<br>' . $connection->connect_error);
        } else {
            $this->connection = $connection;
        }
    }
    
    public function startTransaction() {
        $this->connection->autocommit(false);
    }

    public function runPreparedQuery($query, array $params, $paramTypes) {
        $bindings = array();
        $bindings[] = & $paramTypes;
        $paramsLen = count($params);
        for ($i=0; $i<$paramsLen; $i++) {
            $bindings[$i+1] = & $params[$i];
        }
        $statement = $this->connection->prepare($query);
        if ($statement===false) {
            throw new Exception('Statement error in '.$query.'.<br>'.$this->connection->error.'.');
        } else {
            call_user_func_array(array($statement, 'bind_param'), $bindings);
            $statement->execute();
            $this->queryRes = $statement->get_result();
        }
    }
    
    public function runUpdateQuery($table, array $newValues, array $paramTypesArray, $whereColumn, $whereId, array $oldValues=null) {
        $query = 'UPDATE '.$table.' SET ';
        $params = array();
        $paramTypes = '';
        foreach ($newValues as $column=>$newValue) {
            if (isset($oldValues[$column])) {
                if ($newValue!=$oldValues[$column]) {
                    if ($paramTypes=='') {
                        $query .= $column.'=?';
                    } else {
                        $query .= ', '.$column.'=?';
                    }
                    $params[] = $newValue;
                    $paramTypes .= $paramTypesArray[$column];
                }
            } else {
                if ($paramTypes=='') {
                    $query .= $column.'=?';
                } else {
                    $query .= ', '.$column.'=?';
                }
                $params[] = $newValue;
                $paramTypes .= $paramTypesArray[$column];
            }
        }
        if ($paramTypes!='') {
            $query .= ' WHERE '.$whereColumn.'=?';
            $params[] = $whereId;
            $paramTypes .= 'i';
            $this->runPreparedQuery($query, $params, $paramTypes);
        }
    }

    public function runQuery($query) {
        $this->queryRes = $this->connection->query($query);
        if ($this->queryRes===false) {
            throw new Exception('Query error in '.$query.'.<br>'.$this->connection->error.'.');
        }
    }

    public function getQueryRes() {
        return $this->queryRes;
    }
    
    public function delQueryRes() {
        unset($this->queryRes);
    }
    
    public function endTransaction() {
        $this->connection->commit();
        $this->connection->autocommit(true);
    }

    public function __destruct() {
        if ($this->connection!=null) {
            $this->connection->close();
        }
    }
}