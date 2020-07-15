<?php
function getBuyOption(DbManager $dbManager, $column, $options, $snackId) {
    if (isset($options[$column])) {
        $option = $options[$column];
    } else {
        $dbManager->query('SELECT '.$column.' FROM snacks WHERE id=?', array($snackId), 'i');
        while ($row = $dbManager->result->fetch_row()) {
            $option = $row[0];
        }
    }
    return $option;
}
function checkSnackCountable(DbManager $dbManager, $snackId) {
    $dbManager->query('SELECT countable FROM snacks WHERE id=?', array($snackId), 'i');
    $countable = true;
    while ($row = $dbManager->result->fetch_row()) {
        $countable = (bool)$row[0];
    }
    return $countable;
}
function buy(DbManager $dbManager, $userId, $snackId, $quantity, array $options) {
    $isCountable = checkSnackCountable($dbManager, $snackId);
    $lockQuery = 'LOCK TABLES snacks READ, outflows WRITE, fund_funds WRITE, actions WRITE';
    if ($isCountable) {
        $lockQuery .= ', crates WRITE, snacks_stock WRITE';
    } else {
        $lockQuery .= ', users READ, users_funds WRITE';
    }
    $dbManager->query($lockQuery);
    $unitPrice = getBuyOption($dbManager, 'price', $options, $snackId);
    $totalPrice = $unitPrice*$quantity;
    $snacksPerBox = getBuyOption($dbManager, 'snacks_per_box', $options, $snackId);
    $snackNumber = $snacksPerBox*$quantity;
    $expirationInDays = getBuyOption($dbManager, 'expiration_in_days', $options, $snackId);
    $dbManager->query('INSERT INTO outflows (amount, snack_id, quantity) VALUES (?, ?, ?)', array($totalPrice, $snackId, $quantity), 'sii');
    $dbManager->query('SELECT id FROM outflows ORDER BY id DESC LIMIT 1');
    $outflowId = 0;
    while ($row = $dbManager->result->fetch_row()) {
        $outflowId = $row[0];
    }
    $dbManager->query('UPDATE fund_funds SET amount=amount-?', array($totalPrice), 'd');
    $dbManager->query('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount, outflow_id) VALUES (?, ?, ?, ?, ?,?)', array($userId, 7, $snackId, $snackNumber, $totalPrice, $outflowId), 'iiiidi');
    if ($isCountable) {
        $dbManager->query('INSERT INTO crates (outflow_id, snack_id, snack_quantity, price_per_snack, expiration) VALUES (?, ?, ?, ?, ?)', array($outflowId, $snackId, $snackNumber, round($unitPrice/$snacksPerBox, 2, PHP_ROUND_HALF_UP), (new DateTime('+'.$expirationInDays.' days'))->format('Y-m-d')), 'iiids');
        $dbManager->query('UPDATE snacks_stock SET quantity=quantity+? WHERE snack_id=?', array($snackNumber, $snackId), 'ii');
    } else {
        $dbManager->query('SELECT id FROM users WHERE active=?', array(1), 'i');
        $userIds = array();
        while ($row = $dbManager->result->fetch_row()) {
            $userIds[] = $row[0];
        }
        $totalPricePerUser = round($totalPrice/count($userIds), 2, PHP_ROUND_HALF_UP);
        foreach ($userIds as $singleUserId) {
            $dbManager->query('UPDATE users_funds SET amount=amount-? WHERE user_id=?', array($totalPricePerUser, $singleUserId), 'di');
        } 
    }
    return array('success'=>true, 'status'=>200);
}
