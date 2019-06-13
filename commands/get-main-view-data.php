<?php
require_once('get-fund-funds.php');
require_once('get-user-funds.php');
require_once('get-actions.php');
function getMainViewData($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $response = array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>getFundFunds(false), 'user-funds-amount'=>getUserFunds($userId, false), 'actions'=>getActions(5, 0, 'DESC', false)));
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
