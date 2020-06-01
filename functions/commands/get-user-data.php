<?php
function getUserData($userId) {
    global $dbManager;
    try {
        $dbManager->query('SELECT name, friendly_name FROM users WHERE id=?', array($userId), 'i');
        while ($row = $dbManager->result->fetch_assoc()) {
            $name = $row['name']; 
            $friendlyName = $row['friendly_name']; 
        }
        $response = array('success'=>true, 'status'=>200, 'data'=>array('user'=>array('name'=>$name, 'friendly-name'=>$friendlyName)));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
