<?php
function checkUserAdmin(DbManager $dbManager, &$response) {
    $dbManager->lockTables(array('users'=>'r'));
    $dbManager->query('SELECT admin FROM users WHERE id=?', array($_SESSION['user-id']), 'i');
    $isAdmin = false;
    while ($row = $dbManager->result->fetch_row()) {
        $isAdmin = (bool)$row[0];
    }
    if (!$isAdmin) {
        $response = array('success'=>true, 'status'=>401, 'message'=>getTranslatedString('response-messages', 7).getTranslatedString('response-messages', 32));
    }
    return $isAdmin;
}
