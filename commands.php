<?php
    function eat($userId, $snackId, $quantity) {
        global $dbManager;
        
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT outflow_id, price_per_snack FROM crates WHERE snack_id=? AND quantity!=0 ORDER BY expiration ASC LIMIT 1', [$snackId], 'i');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $outflow_id = $row['outflow_id'];
            $totalPrice = $quantity*$row['price_per_snack'];
        }
        if (isset($outflow_id)) {
            $dbManager->runPreparedQuery('UPDATE crates SET quantity = quantity-? WHERE outflow_id=?', [$quantity, $outflow_id], 'ii');
            $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity = quantity-? WHERE snack_id=?', [$quantity, $snackId], 'ii');
            $dbManager->runPreparedQuery('UPDATE eaten SET quantity = quantity+? WHERE snack_id=?', [$quantity, $snackId], 'ii');
            $dbManager->runPreparedQuery('UPDATE users_funds SET amount = amount-? WHERE user_id=?', [$totalPrice, $userId], 'si');
            $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity) VALUES (?, ?, ?, ?)', [$userId, 1, $snackId, $quantity], 'iiii');
            $response = array('success'=>true, 'code'=>204);
        } else {
            $response = array('success'=>false, 'code'=>404, 'message'=>'No crates containing snack id '.$snackId.'.');
        }
        $dbManager->endTransaction();
        $dbManager->delQueryRes();
        return json_encode($response);
    }
    
    function buy($snackId, $options=null) { // options array: price, snack per box, expiration in days
        
    }
?>
