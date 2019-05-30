<?php
function getUserData($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT name, friendly_name FROM users WHERE id=?', array($userId), 'i');
        while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $name = $usersRow['name']; 
            $friendlyName = $usersRow['friendly_name']; 
        }
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200, 'data'=>array('user'=>array('name'=>$name, 'friendly-name'=>$friendlyName)));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function getSnacksData() {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('SELECT id, name, friendly_name, price, snacks_per_box, expiration_in_days FROM snacks ORDER BY friendly_name ASC');
        while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $snacks[] = array('id'=>$snacksRow['id'], 'name'=>$snacksRow['name'], 'friendly-name'=>$snacksRow['friendly_name'], 'price'=>$snacksRow['price'], 'snacks-per-box'=>$snacksRow['snacks_per_box'], 'expiration-in-days'=>$snacksRow['expiration_in_days']);
        }
        $dbManager->endTransaction();
        $response['success'] = true; 
        if (isset($snacks)) {
            $response['status'] = 200;
            $response['data']['snacks'] = $snacks;
        } else {
            $response['status'] = 204;
        }

    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function getSnackData($snackId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT id, friendly_name, price, snacks_per_box, expiration_in_days FROM snacks WHERE id=?', array($snackId), 'i');
        while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $snack = array('id'=>$snacksRow['id'], 'friendly-name'=>$snacksRow['friendly_name'], 'price'=>$snacksRow['price'], 'snacks-per-box'=>$snacksRow['snacks_per_box'], 'expiration-in-days'=>$snacksRow['expiration_in_days']);
        }
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200, 'data'=>array('snack'=>$snack));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function insertEdits($newValues, $types, $oldValues) {
    global $dbManager;
    $dbManager->runQuery('SELECT id FROM actions ORDER BY id DESC LIMIT 1');
    while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
        $actionId = $row['id'];
    }
    foreach($newValues as $column=>$newValue) {
        $type = $types[$column];
        if (isset($oldValues[$column])) {
            if ($oldValues[$column]!=$newValue) {
                $dbManager->runPreparedQuery('INSERT INTO edits (action_id, column_name, old_'.$type.'_value, new_'.$type.'_value) VALUES (?, ?, ?, ?)', array($actionId, $column, $oldValues[$column], $newValue), 'is'.$type.$type);
            }
        } else {
            $dbManager->runPreparedQuery('INSERT INTO edits (action_id, column_name, new_'.$type.'_value) VALUES (?, ?, ?)', array($actionId, $column, $newValue), 'is'.$type);
        }
    }
}

function editSnackOrUser(array $ids, array $newValues, array $types) {
    global $dbManager;
    if (isset($ids['snack'])) {
        $table = 'snacks';
        $whereId = $ids['snack'];
        $oldValueCheckExceptions = null;
    } else {
        $table = 'users';
        $whereId = $ids['user'];
        $oldValueCheckExceptions = array('password');
    }
    try {
        $dbManager->startTransaction();
        $oldValues = $dbManager->getOldValues($newValues, $table, 'id', $whereId, $oldValueCheckExceptions);
        if ($dbManager->runUpdateQuery($table, $newValues, $types, 'id', $whereId, $oldValues)) {
            if ($table=='snacks') {
                $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($ids['user'], 5, $ids['snack']), 'iii');
            } else {
                $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($ids['user'], 2), 'ii');
            }
            insertEdits($newValues, $types, $oldValues);
        }
        if ($table=='users' && isset($newValues['friendly_name']) && $newValues['friendly_name']!=$oldValues['friendly_name']) {
            $_SESSION['user-friendly-name'] = $newValues['friendly_name'];
            if (filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN)) {
                setcookie('user-friendly-name', $newValues['friendly_name'], time()+86400*5);
            } else {
                setcookie('user-friendly-name', $newValues['friendly_name'], 0);
            }
        }
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function deposit($userId, $amount) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('INSERT INTO inflows (user_id, amount) VALUES (?,?)', array($userId, $amount), 'id');
        $dbManager->runPreparedQuery('UPDATE users_funds SET amount=amount+? WHERE user_id=?', array($amount, $userId), 'di');
        $dbManager->runPreparedQuery('UPDATE fund_funds SET amount=amount+?', array($amount), 'd');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, funds_amount) VALUES (?,?,?)', array($userId, 3, $amount), 'iid');
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function addSnack($userId, $name, $price, $snacksPerBox, $expirationInDays, $countable) {
    global $dbManager;
    try {
        $subjectUserId = $userId;
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('INSERT INTO snacks (name, friendly_name, price, snacks_per_box, expiration_in_days, countable) VALUES (?, ?, ?, ?, ?, ?)', array(str_replace(' ', '-', strtolower($name)), $name, $price, $snacksPerBox, $expirationInDays, $countable), 'ssdiii');
        $dbManager->runQuery('SELECT id FROM snacks ORDER BY id DESC LIMIT 1');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $snackId = $row['id'];
        }
        if ($countable) {
            $dbManager->runPreparedQuery('INSERT INTO snacks_stock (snack_id) VALUES (?)', array($snackId), 'i');
            $dbManager->runQuery('SELECT id FROM users');
            while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
                $usersId[] = $row['id'];
            }
            foreach($usersId as $userId) {   
                $dbManager->runPreparedQuery('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', array($snackId, $userId), 'ii');
            }
        }
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($subjectUserId, 4, $snackId), 'iii');
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>201, 'data'=>array('snack-id'=>$snackId));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function getToBuy() {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('SELECT id, friendly_name, price, snacks_per_box, expiration_in_days FROM snacks ORDER BY friendly_name ASC');
        while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $snacks[] = array('id'=>$snacksRow['id'], 'friendly_name'=>$snacksRow['friendly_name'], 'price'=>$snacksRow['price'], 'snacks-per-box'=>$snacksRow['snacks_per_box'], 'expiration-in-days'=>$snacksRow['expiration_in_days']);
        }
        $dbManager->endTransaction();
        $response['success'] = true;
        if (isset($snacks)) {
            $response['status'] = 200;
            $response['data']['snacks'] = $snacks;
        } else {
            $response['status'] = 204;
        }
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function getBuyOptions($column, $options, $snackId) {
    global $dbManager;
    if (isset($options[$column])) {
        $buyOption = $options[$column];
    } else {
        $dbManager->runPreparedQuery('SELECT '.$column.' FROM snacks WHERE id=?', array($snackId), 'i');
        while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $buyOption = $snacksRow[$column];
        }
        if (!isset($buyOption)) {
            throw new Exception('Statement error in SELECT '.$column.' FROM snacks WHERE id='.$snackId.'.<br>Column or id not found.');
        }
    }
    return $buyOption;
}

function checkSnackCountable ($snackId) {
    global $dbManager;
    $dbManager->runPreparedQuery('SELECT countable FROM snacks WHERE id=?', array($snackId), 'i');
    while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
        $countable = $snacksRow['countable'];
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
        $dbManager->runPreparedQuery('UPDATE fund_funds SET amount=amount-?', array($totalPrice), 'd');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount) VALUES (?, ?, ?, ?, ?)', array($userId, 6, $snackId, $snackNumber, $totalPrice), 'iiiid');
        if (checkSnackCountable($snackId)) {
            $dbManager->runPreparedQuery('INSERT INTO crates (outflow_id, snack_id, snack_quantity, price_per_snack, expiration) VALUES (?, ?, ?, ?, ?)', array($outflowId, $snackId, $snackNumber, round($unitPrice/$snacksPerBox, 2, PHP_ROUND_HALF_UP), date('Y-m-d', strtotime('+'.$expirationInDays.' days'))), 'iiids');
            $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity=quantity+? WHERE snack_id=?', array($snackNumber, $snackId), 'ii');
        } else {
            $dbManager->runQuery('SELECT id FROM users');
            $userIds = array();
            while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
                $userIds[] = $usersRow['id'];
            }
            $totalPricePerUser = round($totalPrice/count($userIds), 2, PHP_ROUND_HALF_UP);
            foreach ($userIds as $singleUserId) {
                $dbManager->runPreparedQuery('UPDATE users_funds SET amount=amount-? WHERE user_id=?', array($totalPricePerUser, $singleUserId), 'di');
            } 
        }
        $dbManager->endTransaction(); 
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function getToEatAndUserFunds($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
		$userFundsAmount = getUserFunds($userId, false);
		$snacks = array();
        $dbManager->runPreparedQuery('SELECT snacks_stock.snack_id, snacks_stock.quantity, (SELECT crates.expiration FROM crates WHERE crates.snack_id=snacks_stock.snack_id ORDER BY crates.expiration ASC LIMIT 1) as expiration FROM snacks_stock WHERE snacks_stock.quantity!=? ORDER BY expiration ASC', array(0), 'i');
        while ($snacksStockRow = $dbManager->getQueryRes()->fetch_assoc()) {
			$snacks[] = array('id'=>$snacksStockRow['snack_id'], 'quantity'=>$snacksStockRow['quantity'], 'expiration'=>$snacksStockRow['expiration']);
		}
		foreach ($snacks as &$snack) {
			$dbManager->runPreparedQuery('SELECT price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=? ORDER BY expiration ASC LIMIT 1', array($snack['id'], 0), 'ii');
			while ($cratesRow = $dbManager->getQueryRes()->fetch_assoc()) {
				$snack['price-per-snack'] = $cratesRow['price_per_snack'];
			}
			$dbManager->runPreparedQuery('SELECT name, friendly_name FROM snacks WHERE id=?', array($snack['id']), 'i');
			while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
				$snack['name'] = $snacksRow['name'];
				$snack['friendly-name'] = $snacksRow['friendly_name'];
			}
        }
        $dbManager->endTransaction();
		$response = array('success'=>true, 'status'=>200, 'data'=>array('user-funds-amount'=>$userFundsAmount, 'snacks'=>$snacks));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function eat($userId, $snackId, $quantity) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT outflow_id, price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=? ORDER BY expiration ASC LIMIT 1', array($snackId, 0), 'ii');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $outflowId = $row['outflow_id'];
            $totalPrice = $quantity*$row['price_per_snack'];
        }
        if (isset($outflowId)) {
            $dbManager->runPreparedQuery('UPDATE crates SET snack_quantity = snack_quantity-? WHERE outflow_id=?', array($quantity, $outflowId), 'ii');
            $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity = quantity-? WHERE snack_id=?', array($quantity, $snackId), 'ii');
            $dbManager->runPreparedQuery('UPDATE eaten SET quantity = quantity+? WHERE user_id=? AND snack_id=?', array($quantity, $userId, $snackId), 'iii');
            $dbManager->runPreparedQuery('UPDATE users_funds SET amount = amount-? WHERE user_id=?', array($totalPrice, $userId), 'di');
            $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount) VALUES (?, ?, ?, ?, ?)', array($userId, 7, $snackId, $quantity, $totalPrice), 'iiiid');
            $response = array('success'=>true, 'status'=>200);
        } else {
            $response = array('success'=>false, 'status'=>404, 'message'=>'No crates containing snack id '.$snackId.'.');
        }
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
