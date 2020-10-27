<?php
function getUserData(DbManager $dbManager, $userId) {
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction(MYSQLI_TRANS_START_READ_ONLY);
    }
    $dbManager->query('SELECT name, friendly_name FROM users WHERE id=?', array($userId), 'i');
    while ($row = $dbManager->result->fetch_assoc()) {
        $name = $row['name']; 
        $friendlyName = $row['friendly_name']; 
    }
    $response = array('success'=>true, 'status'=>200, 'data'=>array('user'=>array('name'=>$name, 'friendly-name'=>$friendlyName)));
    return $response;
}
