<?php
function checkUserPassword($userId, $password) {
    global $dbManager;
    $passwordVerified = false;
    $dbManager->startTransaction();
    $dbManager->runQuery('LOCK TABLES users READ');
    $dbManager->runPreparedQuery('SELECT password FROM users WHERE id=?', array($userId), 'i');
    $hashedPassword = $dbManager->getQueryRes()->fetch_row()[0];
    $dbManager->runQuery('UNLOCK TABLES');
    $dbManager->endTransaction();
    if (password_verify($password, $hashedPassword)) {
        $passwordVerified = true;
    }
    return $passwordVerified;
}
