<?php
require('login.php');
function addUser($name, $password, $friendlyName, $admin, $appRequest) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('INSERT INTO users (name, password, friendly_name, admin) VALUES (?, ?, ?, ?)', array($name, password_hash($password, PASSWORD_DEFAULT), $friendlyName, $admin), 'sssi');
        $dbManager->runQuery('SELECT id FROM users ORDER BY id DESC LIMIT 1');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $userId = $row['id'];
        }
        $dbManager->runQuery('SELECT id FROM snacks');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $snackIds[] = $row['id'];
        }
        foreach($snackIds as $snackId) {
            $dbManager->runPreparedQuery('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', array($snackId, $userId), 'ii');
        }
        $dbManager->runPreparedQuery('INSERT INTO users_funds (user_id) VALUES (?)', array($userId), 'i');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($userId, 1), 'ii');
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>201, 'data'=>array('token'=>login($name, $password, false, $appRequest, false)));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
