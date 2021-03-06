<?php
function getBuyOption($column, $options, $snackId) {
    global $dbManager;
    if (isset($options[$column])) {
        $buyOption = $options[$column];
    } else {
        $dbManager->runPreparedQuery('SELECT '.$column.' FROM snacks WHERE id=?', array($snackId), 'i');
        while ($snacksRow = $dbManager->getQueryRes()->fetch_row()) {
            $buyOption = $snacksRow[0];
        }
    }
    return $buyOption;
}
function checkSnackCountable ($snackId) {
    global $dbManager;
    $dbManager->runPreparedQuery('SELECT countable FROM snacks WHERE id=?', array($snackId), 'i');
    while ($snacksRow = $dbManager->getQueryRes()->fetch_row()) {
        $countable = $snacksRow[0];
    }
    if ($countable=='1') {
        $countable = true;
    } else {
        $countable = false;
    }
    return $countable;
}
function buy($userId, $snackId, $quantity, array $options) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $isCountable = checkSnackCountable($snackId);
        $lockQuery = 'LOCK TABLES snacks READ, outflows WRITE, fund_funds WRITE, actions WRITE';
        if ($isCountable) {
            $lockQuery .= ', crates WRITE, snacks_stock WRITE';
        } else {
            $lockQuery .= ', users READ, users_funds WRITE';
        }
        $dbManager->runQuery($lockQuery);
        $unitPrice = getBuyOption('price', $options, $snackId);
        $totalPrice = $unitPrice*$quantity;
        $snacksPerBox = getBuyOption('snacks_per_box', $options, $snackId);
        $snackNumber = $snacksPerBox*$quantity;
        $expirationInDays = getBuyOption('expiration_in_days', $options, $snackId);
        $dbManager->runPreparedQuery('INSERT INTO outflows (amount, snack_id, quantity) VALUES (?, ?, ?)', array($totalPrice, $snackId, $quantity), 'sii');
        $dbManager->runQuery('SELECT id FROM outflows ORDER BY id DESC LIMIT 1');
        $outflowId = $dbManager->getQueryRes()->fetch_assoc()['id'];
        $dbManager->runPreparedQuery('UPDATE fund_funds SET amount=amount-?', array($totalPrice), 'd');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount, outflow_id) VALUES (?, ?, ?, ?, ?,?)', array($userId, 6, $snackId, $snackNumber, $totalPrice, $outflowId), 'iiiidi');
        if ($isCountable) {
            $dbManager->runPreparedQuery('INSERT INTO crates (outflow_id, snack_id, snack_quantity, price_per_snack, expiration) VALUES (?, ?, ?, ?, ?)', array($outflowId, $snackId, $snackNumber, round($unitPrice/$snacksPerBox, 2, PHP_ROUND_HALF_UP), (new DateTime('+'.$expirationInDays.' days'))->format('Y-m-d')), 'iiids');
            $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity=quantity+? WHERE snack_id=?', array($snackNumber, $snackId), 'ii');
        } else {
            $dbManager->runPreparedQuery('SELECT id FROM users WHERE active=?', array(1), 'i');
            $userIds = array();
            while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
                $userIds[] = $usersRow['id'];
            }
            $totalPricePerUser = round($totalPrice/count($userIds), 2, PHP_ROUND_HALF_UP);
            foreach ($userIds as $singleUserId) {
                $dbManager->runPreparedQuery('UPDATE users_funds SET amount=amount-? WHERE user_id=?', array($totalPricePerUser, $singleUserId), 'di');
            } 
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction(); 
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
