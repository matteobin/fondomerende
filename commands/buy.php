<?php
function getBuyOption($column, $options, $snackId) {
    global $dbManager;
    if (isset($options[$column])) {
        $option = $options[$column];
    } else {
        $dbManager->query('SELECT '.$column.' FROM snacks WHERE id=?', [$snackId], 'i');
        while ($row = $dbManager->result->fetch_row()) {
            $option = $row[0];
        }
    }
    return $option;
}
function checkSnackCountable($snackId) {
    global $dbManager;
    $dbManager->query('SELECT countable FROM snacks WHERE id=?', [$snackId], 'i');
    return (bool)$dbManager->result->fetch_row()[0];
}
function buy($userId, $snackId, $quantity, array $options) {
    global $dbManager;
    $isCountable = checkSnackCountable($snackId);
    $lockQuery = 'LOCK TABLES snacks READ, outflows WRITE, fund_funds WRITE, actions WRITE';
    if ($isCountable) {
        $lockQuery .= ', crates WRITE, snacks_stock WRITE';
    } else {
        $lockQuery .= ', users READ, users_funds WRITE';
    }
    $dbManager->query($lockQuery);
    $unitPrice = getBuyOption('price', $options, $snackId);
    $totalPrice = $unitPrice*$quantity;
    $snacksPerBox = getBuyOption('snacks_per_box', $options, $snackId);
    $snackNumber = $snacksPerBox*$quantity;
    $expirationInDays = getBuyOption('expiration_in_days', $options, $snackId);
    $dbManager->query('INSERT INTO outflows (amount, snack_id, quantity) VALUES (?, ?, ?)', [$totalPrice, $snackId, $quantity], 'sii');
    $dbManager->query('SELECT id FROM outflows ORDER BY id DESC LIMIT 1');
    $outflowId = $dbManager->result->fetch_row()[0];
    $dbManager->query('UPDATE fund_funds SET amount=amount-?', [$totalPrice], 'd');
    $dbManager->query('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount, outflow_id) VALUES (?, ?, ?, ?, ?,?)', [$userId, 7, $snackId, $snackNumber, $totalPrice, $outflowId], 'iiiidi');
    if ($isCountable) {
        $dbManager->query('INSERT INTO crates (outflow_id, snack_id, snack_quantity, price_per_snack, expiration) VALUES (?, ?, ?, ?, ?)', [$outflowId, $snackId, $snackNumber, round($unitPrice/$snacksPerBox, 2, PHP_ROUND_HALF_UP), (new DateTime('+'.$expirationInDays.' days'))->format('Y-m-d')], 'iiids');
        $dbManager->query('UPDATE snacks_stock SET quantity=quantity+? WHERE snack_id=?', [$snackNumber, $snackId], 'ii');
    } else {
        $dbManager->query('SELECT id FROM users WHERE active=?', [1], 'i');
        $userIds = [];
        while ($row = $dbManager->result->fetch_row()) {
            $userIds[] = $row[0];
        }
        $totalPricePerUser = round($totalPrice/count($userIds), 2, PHP_ROUND_HALF_UP);
        foreach ($userIds as $singleUserId) {
            $dbManager->query('UPDATE users_funds SET amount=amount-? WHERE user_id=?', [$totalPricePerUser, $singleUserId], 'di');
        } 
    }
    return ['success'=>true, 'status'=>200];
}
