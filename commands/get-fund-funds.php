<?php
function getFundFunds($apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
			$dbManager->startTransaction();
		}
        $dbManager->runQuery('SELECT amount FROM fund_funds');
        while ($fundFundsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $fundFundsAmount = $fundFundsRow['amount'];
        }
		if ($apiCall) {
			$dbManager->endTransaction();
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
