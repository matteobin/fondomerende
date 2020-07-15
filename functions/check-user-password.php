<?php
function checkUserPassword(DbManager $dbManager, $userId, $password) {
    $dbManager->query('SELECT password FROM users WHERE id=?', array($userId), 'i'); 
    $hashedPassword = '';
    while ($row = $dbManager->result->fetch_row()) {
        $hashedPassword = $row[0];
    }
    return password_verify($password, $hashedPassword) ? true : false;
}
