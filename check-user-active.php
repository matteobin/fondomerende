<?php
function checkUserActive() {
    global $dbManager, $response;
    $dbManager->lockTables(array('users'=>'r'));
    $dbManager->query('SELECT active FROM users WHERE id=?', array($_SESSION['user-id']), 'i');
    $isActive = false;
    while ($row = $dbManager->result->fetch_row()) {
        $isActive = (bool)$row[0];
    }
    if (!$isActive) {
        $response = array('success'=>true, 'status'=>401, 'message'=>getTranslatedString('response-messages', 7).getTranslatedString('response-messages', 8));
    }
    return $isActive;
}
