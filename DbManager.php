<?php
class DbManager {
    private $connection = null;
    public $inTransaction = false;
    public $result = null;
    
    public function __construct() {
        $connection = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
        if ($connection) {
            $this->connection = $connection;
        } else {
            throw new Exception('Connection error code '.mysqli_connect_errno().'. '.mysqli_connect_error());
        }
    }

    public function __destruct() {
        if (!is_null($this->connection)) {
            if ($this->inTransaction) {
                $this->connection->commit();
                $this->query('UNLOCK TABLES');
            }
            $this->connection->close();
        }
    }

    public function beginTransactionAndLock(array $tables) {
        if (!$this->inTransaction) {
            $this->connection->autocommit(false);
            $this->inTransaction = true;
        }
        $lockQuery = 'LOCK TABLES ';
        foreach ($tables as $table=>$lockType) {
            $lockType = $lockType=='w' || $lockType=='write' || $lockType==1 ? 'WRITE' : 'READ';
            $lockQuery .= $table.' '.$lockType.', ';
        }
        $lockQuery = substr($lockQuery, 0, -2);
        $this->query($lockQuery);
    }

    public function getByUniqueId($column, $table, $id) {
        $this->query('SELECT '.$column.' FROM '.$table.' WHERE id=?', array($id), 'i');
        while ($row = $this->result->fetch_assoc()) {
            $result = $row[$column];
        }
        return $result;
    }

    public function getOldValues(array $newValues, $table, $whereColumn, $whereId, array $exceptions=null) {
        $oldValues = array();
        foreach($newValues as $column=>$newValue) {
            if (!isset($exceptions[$column])) {
                $this->query('SELECT '.$column.' FROM '.$table.' WHERE '.$whereColumn.'=?', array($whereId), 'i');
                while ($row = $this->result->fetch_assoc()) {
                    $oldValues[$column] = $row[$column];
                }
            }
        }
        return $oldValues;
    }

    public function rollbackTransaction() {
        $this->connection->rollback();
        $this->inTransaction = false;
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

    public function updateQuery($table, array $newValues, array $paramTypesArray, $whereColumn, $whereId, array $oldValues=null) {
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
                    if (isset($paramTypesArray[$column])) {
                        $paramTypes .= $paramTypesArray[$column];
                    } else {
                        $backtrace = debug_backtrace();
                        throw new Exception($column.' type is missing in types array at line '.$backtrace[1]['line'].' in '.$backtrace[1]['file'].'.');
                    }
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
            $this->query($query, $params, $paramTypes);
            return true;
        } else return false;
    }
}
