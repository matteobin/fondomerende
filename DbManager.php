<?php
class DbManager {
    private $connection;
    private $queryRes;
    
    public function __construct($server, $user, $password, $database) {
        $connection = new mysqli($server, $user, $password, $database);
        if ($connection->connect_errno) {
            throw new Exception('Connection error code '.$connection->connect_errno.'. '.$connection->connect_error);
        } else {
            $this->connection = $connection;
        }
    }

    public function __destruct() {
        if ($this->connection!=null) {
            $this->connection->close();
        }
    }

    public function endTransaction() {
        $this->connection->commit();
        $this->connection->autocommit(true);
    }

    public function getByUniqueId($column, $table, $id) {
        $this->runPreparedQuery('SELECT '.$column.' FROM '.$table.' WHERE id=?', array($id), 'i');
        while ($row = $this->getQueryRes()->fetch_assoc()) {
            $result = $row[$column];
        }
        return $result;
    }

    public function getOldValues(array $newValues, $table, $whereColumn, $whereId, array $exceptions=null) {
        $oldValues = array();
        foreach($newValues as $column=>$newValue) {
            if (!isset($exceptions[$column])) {
                $this->runPreparedQuery('SELECT '.$column.' FROM '.$table.' WHERE '.$whereColumn.'=?', array($whereId), 'i');
                while ($row = $this->getQueryRes()->fetch_assoc()) {
                    $oldValues[$column] = $row[$column];
                }
            }
        }
        return $oldValues;
    }

    public function getQueryRes() {
        return $this->queryRes;
    }

    public function rollbackTransaction() {
        $this->connection->rollback();
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
            throw new Exception('Statement error in \''.$query.'\'. '.$this->connection->error.'.');
        } else {
            call_user_func_array(array($statement, 'bind_param'), $bindings);
            if (!$statement->execute()) {
                throw new Exception('Execution error in \''.$query.'\'. '.$this->connection->error.'.');
            }
            $this->queryRes = $statement->get_result();
        }
    }

    public function runQuery($query) {
        $this->queryRes = $this->connection->query($query);
        if ($this->queryRes===false) {
            throw new Exception('Query error in \''.$query.'\'. '.$this->connection->error.'.');
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
            $this->runPreparedQuery($query, $params, $paramTypes);
            return true;
        } else return false;
    }

    public function startTransaction() {
        $this->connection->autocommit(false);
    }
}
