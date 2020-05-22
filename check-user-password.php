<?php
function checkUserPassword($userId, $password) {
    global $dbManager;
    $passwordVerified = false;
    $dbManager->query('SELECT password FROM users WHERE id=?', array($userId), 'i');
    $hashedPassword = $dbManager->result->fetch_row()[0];
    if (password_verify($password, $hashedPassword)) {
        $passwordVerified = true;
    }
    return $passwordVerified;
}
