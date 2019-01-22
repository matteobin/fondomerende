<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/auth-key.php');
$appRequest = false;
if (basename($_SERVER['SCRIPT_FILENAME'])=='process-request.php') {
    $appRequest = true;
}

function checkAuth() {
    $isAuth = false;
    $authKey = filter_input(INPUT_COOKIE, 'auth-key', FILTER_SANITIZE_STRING); 
    if ($authKey==AUTH_KEY) {
        $isAuth = true;
    }
    return $isAuth;
}

function checkRequestMethod($acceptedMethod) {
    $requestMethodRight = true;
    global $requestMethod;
    if ($requestMethod!=$acceptedMethod) {
        $requestMethodRight = false;
        global $response;
        $response = array('success'=>false, 'status'=>405, 'message'=>'Invalid request: you should send it through '.$acceptedMethod.' instead.');
    }
    return $requestMethodRight;
}

function checkUserToken() {
    $isAuth = false;
    global $userToken;
    $userToken = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if ($_SESSION['user-token']==$userToken) {
        $isAuth = true;
    } else {
        global $response;
        $response['response'] = array('success'=>false, 'status'=>401, 'message'=>'Invalid user token: missing or expired.');
    }
    return $isAuth;
}

function getIdByUniqueName($table, $name) {
    global $dbManager;
    $dbManager->runPreparedQuery('SELECT id FROM '.$table.' WHERE name=? LIMIT 1', array($name), 's');
    while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
        $id = $row['id'];
    }
    return $id;
}

function checkFilteredInputValidity($value, $options=null) {
    $valid = true;
    $message = '';
	if (!isset($options['can-be-empty'])) {
		$options['can-be-empty'] = false;
	}
    if ($value===null) {
        $valid = false;
        $message = 'value missing.';
    } else if ($value==='' && !$options['can-be-empty']) {
        $valid = false;
        $message = 'value in wrong format.';
    }
    else if (isset($options['max-length']) && strlen($value)>$options['max-length']) {
        $valid = false;
        $message = '\''.$value.'\' longer than '.$options['max-length'].' characters.';
    }
    else if (isset($options['greater-than']) && $value<=$options['greater-than']) {
        $valid = false;
        $message = '\''.$value.'\' lesser than '.($options['greater-than']+1).'.';
    }
    if ($valid && isset($options['contains'])) {
        foreach ($options['contains'] as $needle) {
            if (strpos($value, $needle)===false) {
                $valid = false;
                $message = '\''.$value.'\' does not contain \''.$needle.'\'.';
                break;
            }
        }
    }
    if ($valid && isset($options['digits-number'])) {
        if (strpos($value, '.')===false) {
            $dotsNumber = 0;
        } else {
            $dotsNumber = 1;
        }
        if (strpos($value, '+')===false && strpos($value, '-')===false) {
            $signsNumber = 0;
        } else {
            $signsNumber = 1;
        }
        if (strlen($value)-$dotsNumber-$signsNumber>$options['digits-number']) {
            $valid = false;
            $message = '\''.$value.'\' has more digits than '.$options['digits-number'].'.';
        }
    }
    if ($valid && isset($options['decimals-number']) && strlen($value)-(strpos($value, '.')+1)>$options['decimals-number']) {
        $valid = false;
        $message = '\''.$value.'\' has more decimals than '.$options['decimals-number'].'.'; 
    }
    if ($valid && isset($options['database'])) {
        $isException = false;
        if (isset($options['database']['exceptions'])) {
            foreach ($options['database']['exceptions'] as $exception) {
                if ($value==$exception) {
                    $isException = true;
                    break;
                }
            }
        }
        if (!$isException) {
            global $dbManager;
            $selectColumn = $options['database']['select-column'];
            $table = $options['database']['table'];
            $valueType = $options['database']['value-type'];
            $query = 'SELECT '.$selectColumn.' FROM '.$table.' WHERE '.$selectColumn.'=?';
            $params[] = $value;
            $types = $valueType;
            $additionalWheres = false;
            if (isset($options['database']['wheres'])) {
                $additionalWheres = true;
                foreach($options['database']['wheres'] as $where) {
                    $query .= ' AND '.$where['column'].'=?';
                    $params[] = $where['value'];
                    $types .= $where['type'];
                }
            }
            $checkType = $options['database']['check-type'];
            if ($checkType=='insert-unique') {
                $insertUnique = true; 
            } else {
                $insertUnique = false;
            }
            $dbManager->runPreparedQuery($query, $params, $types);
            $dbValue = null;
            while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
                $dbValue = $row[$selectColumn];
            }
            if ($insertUnique && $dbValue!=null) {
                $valid = false;
                $message = $value.' is already present in database '.$table.' table at '.$selectColumn.' column.';
            } else if (!$insertUnique && $dbValue===null) {
                $valid = false;
                $message = $value.' is not present in database '.$table.' table at '.$selectColumn.' column';
                if ($additionalWheres) {
                    foreach($options['database']['wheres'] as $where) {
                        $message .= ', where '.$where['column'].' column is '.$where['value'];
                    }
                }
                $message .= '.';
            } 
        }
    }
    return array('valid'=>$valid, 'message'=>$message);
}

function setRequestInputValue(&$valueDestination, $mandatory, $requestVariableName, array $inputFilters, array $validityOptions) {
    $dbColumnValueName = str_replace('-', '_', $requestVariableName);
    $filter = $inputFilters['filter'];
    $filterOptions = null;
    $noInputError = true;
    if (isset($inputFilters['options'])) {
        $filterOptions = $inputFilters['options'];
    }
	global $requestMethod, ${'_'.$requestMethod};
    if ($mandatory || isset(${'_'.$requestMethod}[$requestVariableName])) {
        $value = filter_input(constant('INPUT_'.$requestMethod), $requestVariableName, $filter, $filterOptions);
        $checkResult = checkFilteredInputValidity($value, $validityOptions);
        if ($checkResult['valid']) {
            if (gettype($valueDestination)=='array') {
                $valueDestination[$dbColumnValueName] = $value;
            } else {
                $valueDestination = $value;
            }
        } else {
            global $response;
            $response['response'] = array('success'=>false, 'status'=>400, 'message'=>'Invalid '.str_replace('-', ' ', $requestVariableName).': '.$checkResult['message']);
			$noInputError = false;
        }
    }
    return $noInputError;
}

function checkUserPassword($userId, $password) {
    global $dbManager;
    $passwordVerified = false;
    $dbManager->startTransaction();
    $dbManager->runPreparedQuery('SELECT password FROM users WHERE id=?', array($userId), 'i');
    while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
        $hashedPassword = $usersRow['password'];
    }
    $dbManager->endTransaction();
    if (password_verify($password, $hashedPassword)) {
        $passwordVerified = true;
    }
    return $passwordVerified;
}

$requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
$response['response'] = array('success'=>false, 'status'=>400, 'message'=>'Invalid request: no parameters were sent.');
if (checkAuth()) {
	require_once(__ROOT__.'/lib/DbManager/DbManager.php');
	$dbManager = new DbManager();
    if (setRequestInputValue($commandName, true, 'command-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>25, 'database'=>array('table'=>'commands', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence', 'exceptions'=>array('login', 'logout', 'get-last-actions', 'get-main-view-data', 'get-user-data', 'get-snacks-data', 'get-snack-data', 'get-user-funds', 'get-fund-funds', 'get-to-buy-and-fund-funds', 'get-to-eat-and-user-funds'))))) {
		require_once(__ROOT__.'/commands.php');
        switch ($commandName) {
            case 'add-user':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!setRequestInputValue($name, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setRequestInputValue($password, true, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
                    break;
                }
                if (!setRequestInputValue($friendlyName, true, 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60))) {
                    break;
                }
                $response = addUser($name, $password, $friendlyName, $appRequest);
                break;
            case 'login':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!setRequestInputValue($userName, true, 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>15))) {
                    break;
                }
                if (!setRequestInputValue($password, true, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>255))) {
                    break;
                }
				$rememberUser = false;
				if (!setRequestInputValue($rememberUser, false, 'remember-user', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
					break;
				}
                $response = login($userName, $password, $rememberUser, $appRequest);
                break;
			case 'logout':
				if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
				$response = logout($appRequest);
				break;
            case 'get-last-actions':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $actionsNumber = 5;
                if (!setRequestInputValue($actionsNumber, false, 'actions-number', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0))) {
                    break;
                }
                $response = getLastActions($actionsNumber);
                break;
            case 'get-main-view-data':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getMainViewData($_SESSION['user-id']);
                break;
            case 'get-user-data':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getUserData($_SESSION['user-id']);
                break;
            case 'edit-user':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $values = array();
                if (!setRequestInputValue($values, false, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique', 'exceptions'=>array($dbManager->getByUniqueId('name', 'users', $_SESSION['user-id'])))))) {
                    break;
                } else if (isset($values['name'])) {
                    $types['name'] = 's';
                }
                if (!setRequestInputValue($values, false, 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60))) {
                    break;
                } else if (isset($values['friendly_name'])) {
                    $types['friendly_name'] = 's';
                }
                if ($appRequest) {
                    if (!setRequestInputValue($values, false, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
                        break;
                    }
                } else {
                    if (!setRequestInputValue($values, false, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125, 'can-be-empty'=>true))) {
                        break;
                    }
                    if ($values['password']=='') {
                        unset($values['password']);
                    }
                    if (!setRequestInputValue($currentPassword, true, 'current-password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
                        break;
                    }
                    if (!checkUserPassword($_SESSION['user-id'], $currentPassword)) {
                        $response['response'] = array('success'=>false, 'status'=>401, 'message'=>'Wrong password!');
                        break;
                    }
                }
                if (isset($values['password'])) {
                    $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
                    $types['password'] = 's';
                }
                $response = editSnackOrUser(array('user'=>$_SESSION['user-id']), $values, $types);
                break;
            case 'get-user-funds':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getUserFunds($_SESSION['user-id']);
                break;
            case 'deposit':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!$appRequest) {
                   if (!setRequestInputValue($userFundsAmount, false, 'user-funds-amount', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('contains'=>array('.'), 'digits-number'=>4, 'decimals-number'=>2))) {
                        break;
                    } 
                }
                if (!setRequestInputValue($amount, true, 'amount', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greater-than'=>0, 'contains'=>array('.'), 'digits-number'=>4, 'decimals-number'=>2))) {
                    break;
                }
                $response = deposit($_SESSION['user-id'], $amount);
                break;
            case 'add-snack':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($name, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setRequestInputValue($price, true, 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greater-than'=>0, 'contains'=>array('.'), 'digits-number'=>4, 'decimals-number'=>2))) {
                    break;
                }
                if (!setRequestInputValue($snacksPerBox, true, 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'digits-number'=>2))) {
                    break;
                }
                if (!setRequestInputValue($expirationInDays, true, 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'digits-number'=>4))) {
                    break;
                }
                $isLiquid = false;
                if (!setRequestInputValue($isLiquid, false, 'is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
                    break;
                }
                $response = addSnack($_SESSION['user-id'], $name, $price, $snacksPerBox, $expirationInDays, $isLiquid);
                break;
            case 'get-snacks-data':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getSnacksData();
                break;
            case 'get-snack-data':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($snackName, true, 'snack-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $snackId = getIdByUniqueName('snacks', $snackName);
                $response = getSnackData($snackId);
                break;
            case 'edit-snack':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($snackId, true, 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                    break;
                }
                $values = array();
                if (!setRequestInputValue($values, false, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'friendly_name', 'value-type'=>'s', 'check-type'=>'insert-unique', 'exceptions'=>array($dbManager->getByUniqueId('friendly_name', 'snacks', $snackId)))))) {
                    echo('ciao');
                    var_dump($dbManager->getByUniqueId('name', 'snacks', $snackId));
                    break;
                } else if (isset($values['name'])) {
                    $types['name'] = 's';
                    $values['friendly_name'] = $values['name'];
                    $types['friendly_name'] = 's';
                    $values['name'] = str_replace(' ', '-', strtolower($values['name']));
                }
                if (!setRequestInputValue($values, false, 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greater-than'=>0, 'contains'=>array('.'), 'digits-number'=>4, 'decimals-number'=>2))) {
                    break;
                } else if (isset($values['price'])) {
                    $types['price'] = 'd';
                }
                if (!setRequestInputValue($values, false, 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'digits-number'=>2))) {
                    break;
                } else if (isset($values['snacks_per_box'])) {
                    $types['snacks_per_box'] = 'i';
                }
                if (!setRequestInputValue($values, false, 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'digits-number'=>4))) {
                    break;
                } else if (isset($values['expiration_in_days'])) {
                    $types['expiration_in_days'] = 'i';
                }
                if (!setRequestInputValue($values, false, 'is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
                    break;
                } else if (isset($values['is_liquid'])) {
                    $types['is_liquid'] = 'i';
                }
                $response = editSnackOrUser(array('user'=>$_SESSION['user-id'], 'snack'=>$snackId), $values, $types);
                break;
            case 'get-fund-funds':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getFundFunds();
                break;
            case 'get-to-buy-and-fund-funds':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getToBuyAndFundFunds();
                break;
            case 'buy':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($snackId, true, 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                    break;
                }
                if (!setRequestInputValue($quantity, true, 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0))) {
                    break;
                }
				$customiseBuyOptions = false;
				if (!$appRequest) {
					if (!setRequestInputValue($customiseBuyOptions, false, 'customise-buy-options', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
						break;
					}
				}
                $options = array();
				if ($appRequest || $customiseBuyOptions) {
					if (!setRequestInputValue($options, false, 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greater-than'=>0, 'contains'=>array('.'), 'digits-number'=>4, 'decimals-number'=>2))) {
						break;
					}
					if (!setRequestInputValue($options, false, 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'digits-number'=>2))) {
						break;
					}
					if (!setRequestInputValue($options, false, 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'digits-number'=>4))) {
						break;
					}
				}
                $response = buy($_SESSION['user-id'], $snackId, $quantity, $options);
                break;
            case 'get-to-eat-and-user-funds':
                if (!checkRequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getToEatAndUserFunds($_SESSION['user-id']);
                break;
            case 'eat':
                if (!checkRequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($snackId, true, 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                    break;
                }
                $quantity = 1;
                if (!setRequestInputValue($quantity, false, 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greater-than'=>0))) {
                    break;
                }
                $response = eat($_SESSION['user-id'], $snackId, $quantity);
                break;
        }
    }
} else {
    $response['response'] = array('success'=>false, 'status'=>401, 'message'=>'Invalid request: missing or wrong auth key.');
}
if ($appRequest) {
    unset($_COOKIE['auth-key']);
    unset($_COOKIE['user-token']);
    setcookie('auth-key', '', time()-3600);
    setcookie('user-token', '', time()-3600);
	header('Content-Type: application/json');
	echo(json_encode($response));
}