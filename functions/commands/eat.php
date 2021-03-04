<?php
function eat(DbManager $dbManager, $userId, $snackId, $quantity) {
    $dbManager->query('SELECT outflow_id, price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=? ORDER BY expiration ASC LIMIT 1', array($snackId, 0), 'ii');
    $cratesRow = $dbManager->result->fetch_assoc();
    $outflowId = $cratesRow['outflow_id'];
    $totalPrice = $quantity*$cratesRow['price_per_snack'];
    if (isset($outflowId)) {
        $dbManager->query('UPDATE crates SET snack_quantity = snack_quantity-? WHERE outflow_id=?', [$quantity, $outflowId], 'ii');
        $dbManager->query('UPDATE snacks_stock SET quantity = quantity-? WHERE snack_id=?', [$quantity, $snackId], 'ii');
        $dbManager->query('UPDATE eaten SET quantity = quantity+? WHERE user_id=? AND snack_id=?', [$quantity, $userId, $snackId], 'iii');
        $dbManager->query('UPDATE users_funds SET amount = amount-? WHERE user_id=?', [$totalPrice, $userId], 'di');
        $dbManager->query('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount, outflow_id) VALUES (?, ?, ?, ?, ?, ?)', array($userId, 8, $snackId, $quantity, $totalPrice, $outflowId), 'iiiidi');
        return array('success'=>true, 'status'=>200);
    } else {
        return array('success'=>false, 'status'=>404, 'message'=>getStringInLang('response-messages', 31).$snackId.'.');
    }
}
