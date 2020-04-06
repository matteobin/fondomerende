<?php
require 'execute-deposit-or-withdraw-queries.php';
function deposit($userId, $amount) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        executeDepositOrWithdrawQueries($userId, $amount);
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
