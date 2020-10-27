<?php
function checkUserActive(DbManager $dbManager, &$response, $readTransaction=false) {
    if (!$dbManager->transactionBegun) {
        $transactionFlag = $readTransaction ? MYSQLI_TRANS_START_READ_ONLY : MYSQLI_TRANS_START_READ_WRITE;
        $dbManager->beginTransaction($transactionFlag);
    }
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
