<?php
class DbManager {
	private $connection;
    private $queryRes;
	
	public function __construct($server, $user, $psw, $db) {
		$connection = new mysqli($server, $user, $psw, $db);
		if ($connection->connect_error) {
			die('Connection ERROR ' . $connection->connect_errno . '!<br>' . $connection->connect_error);
		} else {
			$this->connection = $connection;
		}
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
            die('Statement ERROR!<br>' . $this->connection->error);
        } else {
            call_user_func_array(array($statement, 'bind_param'), $bindings);
            $this->connection->autocommit(false);
            $statement->execute();
            $this->connection->commit();
            $this->connection->autocommit(true);
            $this->queryRes = $statement->get_result();
        }
    }

    public function runQuery($query) {
        $this->connection->autocommit(false);
        $this->queryRes = $this->connection->query($query);
        if ($this->queryRes===false) {
            die('Query ERROR!<br>' . $this->connection->error);
        } else {
            $this->connection->commit();
            $this->connection->autocommit(true);
        }
    }

    public function getQueryRes() {
        return $this->queryRes;
    }
    
    public function delQueryRes() {
        unset($this->queryRes);
    }
    
    public function getLastError() {
        return $this->connection-error;
    }

    public function __destruct() {
		if ($this->connection!=null) {
			$this->connection->close();
		}
	}
}
?> 
