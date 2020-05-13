<?php
function getUserFunds($userId, $apiCall=true) {
    global $dbManager;
    try {
        $dbManager->query('SELECT amount FROM users_funds WHERE user_id=?', array($userId), 'i');
        $userFundsAmount = $dbManager->result->fetch_row()[0];
        if ($apiCall) {
            $response = array('success'=>true, 'status'=>200, 'data'=>array('user-funds-amount' =>$userFundsAmount));
        }
    } catch (Exception $exception) {
        if ($apiCall) {
            $dbManager->rollbackTransaction();
            $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
        } else {
            throw new Exception($exception->getMessage());
        }
    }
    if ($apiCall) {
        return $response;
    } else {
        return $userFundsAmount;
    }
}
