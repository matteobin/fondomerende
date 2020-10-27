<?php
function getUserFunds(DbManager $dbManager, $userId, $apiCall=true) {
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction(MYSQLI_TRANS_START_READ_ONLY);
    }
    $dbManager->query('SELECT amount FROM users_funds WHERE user_id=?', array($userId), 'i');
    $userFundsAmount = 0;
    while ($row = $dbManager->result->fetch_row()) {
        $userFundsAmount = $row[0];
    }
    if ($apiCall) {
        return array('success'=>true, 'status'=>200, 'data'=>array('user-funds-amount' =>$userFundsAmount));
    } else {
        return $userFundsAmount;
    }
}
