<?php
function login($name, $password, $rememberUser, $appRequest, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();
        }
        $dbManager->runPreparedQuery('SELECT id, password FROM users WHERE name=?', array($name), 's');
        $hashedPassword = '';
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $id = $row['id'];
            $hashedPassword = $row['password'];
        }
        var_dump($password);
        if (password_verify($password, $hashedPassword)) {
            $dbManager->runPreparedQuery('UPDATE users SET password=? WHERE id=?', array(password_hash($password, PASSWORD_DEFAULT), $id), 'si');
            $token = bin2hex(random_bytes(12.5)); 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user-logged'] = true;
            $_SESSION['user-id'] = $id;
            $_SESSION['user-token'] = $token;
            if (!$appRequest) {
                setcookie('user-token', $token);
                setcookie('remember-user', $rememberUser);
            }
            if ($apiCall) {
                $response['response'] = array('success'=>true, 'status'=>201);
                if ($appRequest) {
                    $response['data'] = array('token'=>$token);   
                }
            }
        } else if ($apiCall) {
            $response['response'] = array('success'=>false, 'status'=>403, 'message'=>'Invalid login: wrong credentials.');
        }
        if ($apiCall) {
            $dbManager->endTransaction();          
        }
    } catch (Exception $exception) {
        if ($apiCall) {
            $dbManager->rollbackTransaction();
            $response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage()); 
        } else {
            throw new Exception($exception->getMessage());
        }
    }
    if ($apiCall) {
        return $response;
    } else {
        return $token;
    }
}

function addUser($name, $password, $friendlyName, $appRequest) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('INSERT INTO users (name, password, friendly_name) VALUES (?, ?, ?)', array($name, password_hash($password, PASSWORD_DEFAULT), $friendlyName), 'sss');
        $dbManager->runQuery('SELECT id FROM users ORDER BY id DESC LIMIT 1');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $userId = $row['id'];
        }
        $dbManager->runQuery('SELECT id FROM snacks');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $snackIds[] = $row['id'];
        }
        foreach($snackIds as $snackId) {
            $dbManager->runPreparedQuery('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', array($snackId, $userId), 'ii');
        }
        $dbManager->runPreparedQuery('INSERT INTO users_funds (user_id) VALUES (?)', array($userId), 'i');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($userId, 6), 'ii');
        $dbManager->endTransaction();
        $response['response'] = array('success'=>true, 'status'=>201);
        $response['data'] = array('token'=>login($name, $password, false, $appRequest, false));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function logout($userToken) {
	unset($_SESSION['users'][$userToken]);
	$response['response'] = array('success'=>true, 'status'=>200);
	return $response;
}

function addSnack($userId, $name, $price, $snacksPerBox, $expirationInDays, $isLiquid) {
    global $dbManager;
    try {
        $subjectUserId = $userId;
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('INSERT INTO snacks (name, price, snacks_per_box, expiration_in_days, is_liquid) VALUES (?, ?, ?, ?, ?)', array($name, $price, $snacksPerBox, $expirationInDays, $isLiquid), 'sdiii');
        $dbManager->runQuery('SELECT id FROM snacks ORDER BY id DESC LIMIT 1');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $snackId = $row['id'];
        }
        $dbManager->runPreparedQuery('INSERT INTO snacks_stock (snack_id) VALUES (?)', array($snackId), 'i');
        $dbManager->runQuery('SELECT id FROM users');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $usersId[] = $row['id'];
        }
        foreach($usersId as $userId) {   
            $dbManager->runPreparedQuery('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', array($snackId, $userId), 'ii');
        }
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($subjectUserId, 4, $snackId), 'iii');
        $dbManager->endTransaction();
        $response['response'] = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
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
            $dbManager->runPreparedQuery('INSERT INTO edits (action_id, column_name, old_'.$type.'_value, new_'.$type.'_value) VALUES (?, ?, ?, ?)', array($actionId, $column, $oldValues[$column], $newValue), 'is'.$type.$type);
        } else {
            $dbManager->runPreparedQuery('INSERT INTO edits (action_id, column_name, new_'.$type.'_value) VALUES (?, ?, ?)', array($actionId, $column, $newValue), 'is'.$type);
        }
    }
}

function editSnackOrUser(array $ids, array $newValues, array $types, array $oldValues) {
    global $dbManager;
    if (isset($ids['snack'])) {
        $table = 'snacks';
        $whereId = $ids['snack'];
    } else {
        $table = 'users';
        $whereId = $ids['user'];
    }
    try {
        $dbManager->startTransaction();
        if ($dbManager->runUpdateQuery($table, $newValues, $types, 'id', $whereId, $oldValues)) {
            if ($table=='snacks') {
                $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($ids['user'], 5, $ids['snack']), 'iii');
            } else {
                $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($ids['user'], 7), 'ii');
            }
            insertEdits($newValues, $types, $oldValues);
        }
        $dbManager->endTransaction();
        $response['response'] = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function deposit($userId, $amount) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('INSERT INTO inflows (user_id, amount) VALUES (?,?)', array($userId, $amount), 'id');
        $dbManager->runPreparedQuery('UPDATE users_funds SET amount=amount+? WHERE user_id=?', array($amount, $userId), 'di');
        $dbManager->runPreparedQuery('UPDATE fund_funds SET total=total+?', array($amount), 'd');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, funds_amount) VALUES (?,?,?)', array($userId, 3, $amount), 'iid');
        $dbManager->endTransaction();
        $response['response'] = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
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
        $dbManager->runPreparedQuery('INSERT INTO crates (outflow_id, snack_id, snack_quantity, price_per_snack, expiration) VALUES (?, ?, ?, ?, ?)', array($outflowId, $snackId, $snackNumber, $unitPrice/$snacksPerBox, date('Y-m-d', strtotime('+'.$expirationInDays.' days'))), 'iiids');
        $dbManager->runPreparedQuery('UPDATE snacks_stock SET quantity=quantity+? WHERE snack_id=?', array($snackNumber, $snackId), 'ii');
        $dbManager->runPreparedQuery('UPDATE fund_funds SET total=total-?', array($totalPrice), 'd');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount) VALUES (?, ?, ?, ?, ?)', array($userId, 2, $snackId, $snackNumber, $totalPrice), 'iiiid');
        $dbManager->endTransaction(); 
        $response['response'] = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function getEatable() {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT snack_id, quantity FROM snacks_stock WHERE quantity!=?', array(0), 'i');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $snacks[] = array('id'=>$row['snack_id'], 'quantity'=>$row['quantity']);
        }
        if (isset($snacks)) {
            foreach ($snacks as &$snack) {
                $dbManager->runPreparedQuery('SELECT price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=? ORDER BY expiration ASC LIMIT 1', array($snack['id'], 0), 'ii');
                while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
                    $snack['price-per-snack'] = $row['price_per_snack'];
                }
                $dbManager->runPreparedQuery('SELECT name FROM snacks WHERE id=?', array($snack['id']), 'i');
                while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
                    $snack['name'] = $row['name'];
                }
            }
            $response['response'] = array('success'=>true, 'status'=>200);
            $response['data'] = array('snacks'=>$snacks);
        } else {
            $response['response'] = array('success'=>true, 'status'=>204);
        }
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
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
            $dbManager->runPreparedQuery('UPDATE eaten SET quantity = quantity+? WHERE snack_id=?', array($quantity, $snackId), 'ii');
            $dbManager->runPreparedQuery('UPDATE users_funds SET amount = amount-? WHERE user_id=?', array($totalPrice, $userId), 'di');
            $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity) VALUES (?, ?, ?, ?)', array($userId, 1, $snackId, $quantity), 'iiii');
            $response['response'] = array('success'=>true, 'status'=>200);
        } else {
            $response['response'] = array('success'=>false, 'status'=>404, 'message'=>'No crates containing snack id '.$snackId.'.');
        }
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
