<?php
function getUserFunds(DbManager $dbManager, $userId, $apiCall=true) {
    $dbManager->query('SELECT amount FROM users_funds WHERE user_id=?', array($userId), 'i');
    $userFundsAmount = $dbManager->result->fetch_row()[0];
    if ($apiCall) {
        return array('success'=>true, 'status'=>200, 'data'=>array('user-funds-amount' =>$userFundsAmount));
    } else {
        return $userFundsAmount;
    }
}
