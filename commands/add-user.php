<?php
require 'login.php';
function addUser($name, $password, $friendlyName, $admin) {
    global $dbManager;
    $dbManager->query('INSERT INTO users (name, password, friendly_name, admin) VALUES (?, ?, ?, ?)', [$name, password_hash($password, PASSWORD_DEFAULT), $friendlyName, $admin], 'sssi');
    $dbManager->query('SELECT id FROM users ORDER BY id DESC LIMIT 1');
    $userId = $dbManager->getQueryRes()->fetch_row()[0];
    $dbManager->query('SELECT id FROM snacks');
    while ($row = $dbManager->result->fetch_row()) {
        $snackIds[] = $row[0];
    }
    foreach($snackIds as $snackId) {
        $dbManager->query('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', [$snackId, $userId], 'ii');
    }
    $dbManager->query('INSERT INTO users_funds (user_id) VALUES (?)', [$userId], 'i');
    $dbManager->query('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', [$userId, 1], 'ii');
    return ['success'=>true, 'status'=>201, 'data'=>['token'=>login($name, $password, false, false)]];
}
