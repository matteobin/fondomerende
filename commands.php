<?php
function addUser($name, $password, $friendlyName, $admin, $appRequest) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('INSERT INTO users (name, password, friendly_name, admin) VALUES (?, ?, ?, ?)', array($name, password_hash($password, PASSWORD_DEFAULT), $friendlyName, $admin), 'sssi');
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
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($userId, 1), 'ii');
        $dbManager->endTransaction();
        $response['response'] = array('success'=>true, 'status'=>201);
        $response['data'] = array('token'=>login($name, $password, false, $appRequest, false));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

function login($name, $password, $rememberUser, $appRequest, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();
        }
        $dbManager->runPreparedQuery('SELECT id, password, friendly_name FROM users WHERE name=?', array($name), 's');
        $hashedPassword = '';
        while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $id = $usersRow['id'];
            $hashedPassword = $usersRow['password'];
            $friendlyName = $usersRow['friendly_name'];
        }
        if (password_verify($password, $hashedPassword)) {
            $dbManager->runPreparedQuery('UPDATE users SET password=? WHERE id=?', array(password_hash($password, PASSWORD_DEFAULT), $id), 'si');
            $token = bin2hex(random_bytes(12.5)); 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user-id'] = $id;
            $_SESSION['user-token'] = $token;
            $_SESSION['user-friendly-name'] = $friendlyName;
            if (!$appRequest) {
                if ($rememberUser) {
                    setcookie('user-id', $id, time()+86400*5);
                    setcookie('user-token', $token, time()+86400*5);
                    setcookie('user-friendly-name', $friendlyName, time()+86400*5);
                    setcookie('remember-user', true, time()+86400*5);
                } else {
                    setcookie('user-id', $id, 0);
                    setcookie('user-token', $token, 0);
                    setcookie('user-friendly-name', $friendlyName, 0);
                    setcookie('remember-user', false, 0);
                }
            }
            if ($apiCall) {
                $response['response'] = array('success'=>true, 'status'=>201);
                if ($appRequest) {
                    $response['data'] = array('token'=>$token);   
                }
            }
        } else if ($apiCall) {
            $response['response'] = array('success'=>false, 'status'=>401, 'message'=>'Invalid login: wrong credentials.');
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

function logout($appRequest) {
    if (!$appRequest) {
        unset($_COOKIE['user-id']);
        unset($_COOKIE['user-token']);
        unset($_COOKIE['user-friendly-name']);
        unset($_COOKIE['remember-user']);
        setcookie('user-id', null, time()-3600);
        setcookie('user-token', null, time()-3600);
        setcookie('user-friendly-name', null, time()-3600);
        setcookie('remember-user', null, time()-3600);
    }
    session_unset();
    session_destroy();
	$response['response'] = array('success'=>true, 'status'=>200);
	return $response;
}

function getFundFunds($apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
			$dbManager->startTransaction();
		}
        $dbManager->runQuery('SELECT amount FROM fund_funds');
        while ($fundFundsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $fundFundsAmount = $fundFundsRow['amount'];
        }
		if ($apiCall) {
			$dbManager->endTransaction();
			$response['response'] = array('success'=>true, 'status'=>200);
			$response['data']['fund-funds-amount'] = $fundFundsAmount;
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
		return $fundFundsAmount;
	}
}

function getUserFunds($userId, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
			$dbManager->startTransaction();
		}
        $dbManager->runPreparedQuery('SELECT amount FROM users_funds WHERE user_id=?', array($userId), 'i');
        while ($usersFundsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $userFundsAmount = $usersFundsRow['amount'];
        }
		if ($apiCall) {
			$dbManager->endTransaction();
			$response['response'] = array('success'=>true, 'status'=>200);
			$response['data']['user-funds-amount'] = $userFundsAmount;
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
		return $userFundsAmount;
	}
}

function decodeEdits($editType, $actionId, $userId, $snackId=null) {
    global $dbManager;
    $userEdit = false;
    if ($editType=='user') {
        $userEdit = true;
    } else if (is_null($snackId)) {
        $backtrace = debug_backtrace();
        throw new Exception('decodeEdits function snackId parameter was omitted at line '.$backtrace[0]['line'].' in '.__FILE__.'.');
    }
    $dbManager->runPreparedQuery('SELECT column_name, old_s_value, new_s_value, old_d_value, new_d_value, old_i_value, new_i_value FROM edits WHERE action_id=?', array($actionId), 'i');
    while ($editsRow = $dbManager->getQueryRes()->fetch_assoc()) {
        $edits[$editsRow['column_name']] = array('old-s-value'=>$editsRow['old_s_value'], 'new-s-value'=>$editsRow['new_s_value'], 'old-d-value'=>$editsRow['old_d_value'], 'new-d-value'=>$editsRow['new_d_value'], 'old-i-value'=>$editsRow['old_i_value'], 'new-i-value'=>$editsRow['new_i_value']);
    }
    $decodedEdits = array();
    if (isset($edits)) {
        foreach($edits as $columnName=>$edit) {
            $editSentence = '';
            switch ($columnName) {
                case 'name':
                    if ($userEdit) {
                        if (isset($edits['friendly_name'])) {
                            $editSentence .= $edits['friendly_name']['old-s-value'].' ';
                        } else {
                            $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).' ';
                        }
                        $editSentence .= 'changed his user name from '.$edit['old-s-value'].' to '.$edit['new-s-value'].'.';
                    } else {
                        $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).' changed '.$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).' name from '.$edits['friendly_name']['old-s-value'].' to '.$edits['friendly_name']['new-s-value'].'.';
                    }
                    $decodedEdits[] = $editSentence;
                    break;
                case 'friendly_name':
                    if ($userEdit) {
                        $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).' changed his friendly name from '.$edit['old-s-value'].' to '.$edit['new-s-value'].'.';
                        $decodedEdits[] = $editSentence;
                    }
                    break;
                case 'price':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).' changed '.$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).' price from '.$edit['old-d-value'].' € to '.$edit['new-d-value'].' €.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'snacks_per_box':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).' changed '.$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).' snack per box number from '.$edit['old-i-value'].' to '.$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'expiration_in_days':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).' changed '.$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).' expiration days from '.$edit['old-i-value'].' to '.$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
            }
        }
    }
    return $decodedEdits;
}

function decodeActions($actions) {
    global $dbManager;
    $decodedActions = array();
    foreach($actions as $action) {
        switch ($action['command-id']) {
            case 1:
                $decodedActions[] = $action['created-at'].': added '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).'.';
                break;
            case 2:
                $decodedEdits = decodeEdits('user', $action['id'], $action['user-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 3:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).' deposited '.$action['funds-amount'].' €.';
                break;
            case 4:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).' added snack '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 5:
                $decodedEdits = decodeEdits('snack', $action['id'], $action['user-id'], $action['snack-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 6:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).' bought '.$action['snack-quantity'].' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 7:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).' ate '.$action['snack-quantity'].' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
        }
    }
    return $decodedActions;
}

function getLastActions($actionsNumber, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();   
        }
        $dbManager->runPreparedQuery('SELECT * FROM actions ORDER BY created_at DESC LIMIT ?', array($actionsNumber), 'i');
        while ($actionsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $actions[] = array('id'=>$actionsRow['id'], 'user-id'=>$actionsRow['user_id'], 'command-id'=>$actionsRow['command_id'], 'snack-id'=>$actionsRow['snack_id'], 'snack-quantity'=>$actionsRow['snack_quantity'], 'funds-amount'=>$actionsRow['funds_amount'], 'created-at'=>$actionsRow['created_at']);
        }
        $decodedActions = array();
        if (isset($actions)) {
            $decodedActions = decodeActions($actions);
        }
        if ($apiCall) {
            $dbManager->endTransaction();
            $response['response']['success'] = true;
            if (empty($decodedActions)) {
                $response['response']['status'] = 204;
            } else {
                $response['response']['status'] = 200;
                $response['data']['actions'] = $decodedActions;
            } 
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
        return $decodedActions;
    }
}

function getMainViewData($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $fundFundsAmount = getFundFunds(false);
        $userFundsAmount = getUserFunds($userId, false);
        $actions = getLastActions(5, false);
        $dbManager->endTransaction();
        $response['response'] = array('success'=>true, 'status'=>200);
        $response['data']['fund-funds-amount'] = $fundFundsAmount;
        $response['data']['user-funds-amount'] = $userFundsAmount;
        $response['data']['actions'] = $actions;
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}

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
        $response['response'] = array('success'=>true, 'status'=>200);
        $response['data']['user'] = array('name'=>$name, 'friendly-name'=>$friendlyName);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
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
        $response['response']['success'] = true; 
        if (isset($snacks)) {
            $response['response']['status'] = 200;
            $response['data']['snacks'] = $snacks;
        } else {
            $response['response']['status'] = 204;
        }

    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
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
        $response['response'] = array('success'=>true, 'status'=>200);
        $response['data']['snack'] = $snack;
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
        $dbManager->runPreparedQuery('UPDATE fund_funds SET amount=amount+?', array($amount), 'd');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, funds_amount) VALUES (?,?,?)', array($userId, 3, $amount), 'iid');
        $dbManager->endTransaction();
        $response['response'] = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
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
        $response['response'] = array('success'=>true, 'status'=>201);
        $response['response']['data']['snack-id'] = $snackId;
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
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
        $response['response']['success'] = true;
        if (isset($snacks)) {
            $response['response']['status'] = 200;
            $response['data']['snacks'] = $snacks;
        } else {
            $response['response']['status'] = 204;
        }
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
        $response['response'] = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response['response'] = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
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
		$response['response'] = array('success'=>true, 'status'=>200);
		$response['data']['user-funds-amount'] = $userFundsAmount;
		$response['data']['snacks'] = $snacks;
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
            $dbManager->runPreparedQuery('UPDATE eaten SET quantity = quantity+? WHERE user_id=? AND snack_id=?', array($quantity, $userId, $snackId), 'iii');
            $dbManager->runPreparedQuery('UPDATE users_funds SET amount = amount-? WHERE user_id=?', array($totalPrice, $userId), 'di');
            $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id, snack_quantity, funds_amount) VALUES (?, ?, ?, ?, ?)', array($userId, 7, $snackId, $quantity, $totalPrice), 'iiiid');
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
