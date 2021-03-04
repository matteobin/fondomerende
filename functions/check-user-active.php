<?php
function checkUserActive(DbManager $dbManager, &$response, $readTransaction=false) {
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction($readTransaction);
    }
    $dbManager->query('SELECT active FROM users WHERE id=?', array($_SESSION['user-id']), 'i');
    $isActive = false;
    while ($row = $dbManager->result->fetch_row()) {
        $isActive = (bool)$row[0];
    }
    if (!$isActive) {
        $response = array('success'=>true, 'status'=>401, 'message'=>getStringInLang('response-messages', 7).getStringInLang('response-messages', 8));
    }
    return $isActive;
}
