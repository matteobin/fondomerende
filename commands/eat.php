<?php
function eat($userId, $snackId, $quantity) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT outflow_id, price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=? ORDER BY expiration ASC LIMIT 1', array($snackId, 0), 'ii');
        $cratesRow = $dbManager->getQueryRes()->fetch_assoc();
        $outflowId = $cratesRow['outflow_id'];
        $totalPrice = $quantity*$cratesRow['price_per_snack'];
        if (isset($outflowId)) {
            $dbManager->runPreparedQuery('UPDATE crates SET snack_quantity = snack_quantity-? WHERE outflow_id=?', array($quantity, $outflowId), 'ii');
            $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity = quantity-? WHERE snack_id=?', array($quantity, $snackId), 'ii');
            $dbManager->runPreparedQuery('UPDATE eaten SET quantity = quantity+? WHERE user_id=? AND snack_id=?', array($quantity, $userId, $snackId), 'iii');
            $dbManager->runPreparedQuery('UPDATE users_funds SET amount = amount-? WHERE user_id=?', array($totalPrice, $userId), 'di');
            $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount) VALUES (?, ?, ?, ?, ?)', array($userId, 7, $snackId, $quantity, $totalPrice), 'iiiid');
            $response = array('success'=>true, 'status'=>200);
        } else {
            $response = array('success'=>false, 'status'=>404, 'message'=>getTranslatedString('response-messages', 25).$snackId.'.');
        }
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
