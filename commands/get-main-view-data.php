<?php
require('get-fund-funds.php');
require('get-user-funds.php');
require('get-actions.php');
function getMainViewData($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $response = array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>getFundFunds(false), 'user-funds-amount'=>getUserFunds($userId, false), 'actions'=>getActions(false, 5, 0, 'DESC', false)));
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
