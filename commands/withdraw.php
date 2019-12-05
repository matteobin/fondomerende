<?php
require 'execute-deposit-or-withdraw-queries.php';
function withdraw($userId, $amount) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES outflows WRITE, users_funds WRITE, fund_funds WRITE, actions WRITE');
        $dbManager->runQuery('SELECT amount FROM fund_funds');
        $fundAmount = $dbManager->getQueryRes()->fetch_row()[0];
        $response['success'] = true;
        if ($amount>$fundAmount) {
            $response['status'] = 404;
            $response['message'] = 'There are not enough fund funds to withdraw '.$amount.' € now.';
        } else {
            executeDepositOrWithdrawQueries($userId, $amount, false);
            $response['status'] = 200;
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
