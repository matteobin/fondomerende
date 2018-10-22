<?php
    function eat($userId, $snackId, $quantity, $jsonResponse=true) {
        global $dbManager;
        try {
            $dbManager->startTransaction();
            $dbManager->runPreparedQuery('SELECT outflow_id, price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=0 ORDER BY expiration ASC LIMIT 1', [$snackId], 'i');
            while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
                $outflowId = $row['outflow_id'];
                $totalPrice = $quantity*$row['price_per_snack'];
            }
            if (isset($outflowId)) {
                $dbManager->runPreparedQuery('UPDATE crates SET snack_quantity = snack_quantity-? WHERE outflow_id=?', array($quantity, $outflowId), 'ii');
                $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity = quantity-? WHERE snack_id=?', array($quantity, $snackId), 'ii');
                $dbManager->runPreparedQuery('UPDATE eaten SET quantity = quantity+? WHERE snack_id=?', array($quantity, $snackId), 'ii');
                $dbManager->runPreparedQuery('UPDATE users_funds SET amount = amount-? WHERE user_id=?', array($totalPrice, $userId), 'si');
                $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity) VALUES (?, ?, ?, ?)', array($userId, 1, $snackId, $quantity), 'iiii');
                $response = array('success'=>true, 'status'=>204);
            } else {
                $response = array('success'=>false, 'status'=>404, 'message'=>'No crates containing snack id '.$snackId.'.');
            }
            $dbManager->endTransaction();
            $dbManager->delQueryRes();
        } catch (Exception $statementException) {
            $response = array('success'=>false, 'status'=>500, 'message'=>$statementException->getMessage());
        }
        if ($jsonResponse) {
            return json_encode($response);
        } else {
            return $response;
        }
    }
    
    function getBuyOptions($column, $options, $snackId) {
        global $dbManager;
        if (isset($options[$column])) {
            $buyOption = $options[$column];
        } else {
            $dbManager->runPreparedQuery('SELECT '.$column.' FROM snacks WHERE id=?', array($snackId), 'i');
            while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
                $buyOption = $row[$column];
            }
            if (!isset($buyOption)) {
                throw new Exception('Statement error in SELECT '.$column.' FROM snacks WHERE id='.$snackId.'.<br>Column or id not found.');
            }
        }
        return $buyOption;
    }
    
    function buy($userId, $snackId, $quantity, $options, $jsonResponse=true) {
        global $dbManager;
        try {
            $dbManager->startTransaction();
            $unitPrice = getBuyOptions('price', $options, $snackId);
            $totalPrice = $unitPrice*$quantity;
            $snacksPerBox = getBuyOptions('snacks_per_box', $options, $snackId);
            $snackNumber = $snacksPerBox*$quantity;
            $expirationInDays = getBuyOptions('expiration_in_days', $options, $snackId);
            $dbManager->runPreparedQuery('INSERT INTO outflows (amount, snack_id, quantity) VALUES (?, ?, ?)', array($totalPrice, $snackId, $quantity), 'sii');
            $dbManager->runQuery('SELECT id FROM outflows ORDER BY id DESC LIMIT 1');
            while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
                $outflowId = $row['id'];
            }
            $dbManager->runPreparedQuery('INSERT INTO crates (outflow_id, snack_id, snack_quantity, price_per_snack, expiration) VALUES (?, ?, ?, ?, ?)', array($outflowId, $snackId, $snackNumber, $unitPrice/$snacksPerBox, date('Y-m-d', strtotime('+'.$expirationInDays.' days'))), 'iiiss');
            $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity=quantity+? WHERE snack_id=?', array($snackNumber, $snackId), 'si');
            $dbManager->runPreparedQuery('UPDATE fund_funds SET total=total-?', array($totalPrice), 's');
            $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount) VALUES (?, ?, ?, ?, ?)', array($userId, 2, $snackId, $snackNumber, $totalPrice), 'iiiis');
            $response = array('success'=>true, 'status'=>204);
            $dbManager->endTransaction();
            $dbManager->delQueryRes();
        } catch (Exception $statementException) {
            $response = array('success'=>false, 'status'=>500, 'message'=>$statementException->getMessage());
        }
        if ($jsonResponse) {
            return json_encode($response);
        } else {
            return $response;
        }
    }
    
    function deposit($userId, $amount, $jsonResponse=true) {
        global $dbManager;
        try {
            $dbManager->startTransaction();
            $dbManager->runPreparedQuery('INSERT INTO inflows (user_id, amount) VALUES (?,?)', array($userId, $amount), 'is');
            $dbManager->runPreparedQuery('UPDATE users_funds SET amount=amount+? WHERE user_id=?', array($amount, $userId), 'si');
            $dbManager->runPreparedQuery('UPDATE fund_funds SET total=total+?', array($amount), 's');
            $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, funds_amount) VALUES (?,?,?)', array($userId, 3, $amount), 'iis');
            $response = array('success'=>true, 'status'=>204);
            $dbManager->endTransaction();
            $dbManager->delQueryRes();
        } catch (Exception $statementException) {
            $response = array('success'=>false, 'status'=>500, 'message'=>$statementException->getMessage());
        }
        if ($jsonResponse) {
            return json_encode($response);
        } else {
            return $response;
        }
    }
    
    function addSnack($name, $price, $snacksPerBox, $isLiquid, $expirationInDays, $jsonResponse=true) {
        global $dbManager;
        try {
            $dbManager->runPreparedQuery('INSERT INTO snacks');
        } catch {
            
        }
        if ($jsonResponse) {
            return json_encode($response);
        } else {
            return $response;
        }
    }
?>
