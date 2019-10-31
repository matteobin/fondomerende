<?php
function getUserFunds($userId, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
			$dbManager->startTransaction();
            $dbManager->runQuery('LOCK TABLES users_funds READ');
		}
        $dbManager->runPreparedQuery('SELECT amount FROM users_funds WHERE user_id=?', array($userId), 'i');
        while ($usersFundsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $userFundsAmount = $usersFundsRow['amount'];
        }
		if ($apiCall) {
            $dbManager->runQuery('UNLOCK TABLES');
			$dbManager->endTransaction();
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
