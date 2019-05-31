<?php
function getUserData($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT name, friendly_name FROM users WHERE id=?', array($userId), 'i');
        while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $name = $usersRow['name']; 
            $friendlyName = $usersRow['friendly_name']; 
        }
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200, 'data'=>array('user'=>array('name'=>$name, 'friendly-name'=>$friendlyName)));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
