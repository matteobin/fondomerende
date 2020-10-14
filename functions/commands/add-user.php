<?php
require COMMANDS_PATH.'login.php';
function addUser(DbManager $dbManager, $name, $password, $friendlyName, $admin) {
    $dbManager->query('INSERT INTO users (name, password, friendly_name, admin) VALUES (?, ?, ?, ?)', array($name, password_hash($password, PASSWORD_DEFAULT), $friendlyName, $admin), 'sssi');
    $dbManager->query('SELECT id FROM users ORDER BY id DESC LIMIT 1');
    $userId = 0;
    while ($row = $dbManager->result->fetch_row()) {
        $userId = $row[0];
    }
    $dbManager->query('SELECT id FROM snacks');
    $snackIds = array();
    while ($row = $dbManager->result->fetch_row()) {
        $snackIds[] = $row[0];
    }
    foreach($snackIds as $snackId) {
        $dbManager->query('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', array($snackId, $userId), 'ii');
    }
    $dbManager->query('INSERT INTO users_funds (user_id) VALUES (?)', array($userId), 'i');
    $dbManager->query('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($userId, 1), 'ii');
    return array('success'=>true, 'status'=>201, 'data'=>array('token'=>login($dbManager, $name, $password, false, false)));
}