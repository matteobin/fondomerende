<?php
function getFundFunds($apiCall=true) {
    global $dbManager;
    try {
        $dbManager->query('SELECT amount FROM fund_funds');
        $fundFundsAmount = $dbManager->result->fetch_row()[0];
        if ($apiCall) {
            $response = array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>$fundFundsAmount));
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
        return $fundFundsAmount;
    }
}
