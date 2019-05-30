<?php
function getMainViewData($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $fundFundsAmount = getFundFunds(false);
        $userFundsAmount = getUserFunds($userId, false);
        $actions = getLastActions(5, false);
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>$fundFundsAmount, 'user-funds-amount'=>$userFundsAmount, 'actions'=>$actions));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}