<?php
require 'execute-deposit-or-withdraw-queries.php';
function withdraw($userId, $amount) {
    global $dbManager;
    try {
        $dbManager->query('SELECT amount FROM fund_funds');
        $fundAmount = $dbManager->result->fetch_row()[0];
        $response['success'] = true;
        if ($amount>$fundAmount) {
            $response['status'] = 404;
            $response['message'] = getTranslatedString('response-messages', 29).$amount.getTranslatedString('response-messages', 30);
        } else {
            executeDepositOrWithdrawQueries($userId, $amount, false);
            $response['status'] = 200;
        }
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
