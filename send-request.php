<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/auth-key.php');
$appRequest = false;
if (basename($_SERVER['SCRIPT_FILENAME'])=='send-request.php') {
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

function checkRequestType($acceptedMethod) {
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
    if (isset($_SESSION['user-logged']) && $_SESSION['user-logged']===true && $_SESSION['user-token']==$userToken) {
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
    if ($value===null) {
        $valid = false;
        $message = 'value missing.';
    } else if ($value==='' && (isset($options['can-be-empty']) && !$options['can-be-empty'])) {
        $valid = false;
        $message = 'value in wrong format.';
    }
    else if (isset($options['maxLength']) && strlen($value)>$options['maxLength']) {
        $valid = false;
        $message = '\''.$value.'\' longer than '.$options['maxLength'].' characters.';
    }
    else if (isset($options['greaterThan']) && $value<=$options['greaterThan']) {
        $valid = false;
        $message = '\''.$value.'\' lesser than '.($options['greaterThan']+1).'.';
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
    if ($valid && isset($options['digitsNumber'])) {
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
        if (strlen($value)-$dotsNumber-$signsNumber>$options['digitsNumber']) {
            $valid = false;
            $message = '\''.$value.'\' has more digits than '.$options['digitsNumber'].'.';
        }
    }
    if ($valid && isset($options['decimalsNumber']) && strlen($value)-(strpos($value, '.')+1)>$options['decimalsNumber']) {
        $valid = false;
        $message = '\''.$value.'\' has more decimals than '.$options['decimalsNumber'].'.'; 
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

function setRequestInputValue(&$valueDestination, $mandatory, $valueName, $requestVariableName, array $inputFilters, array $validityOptions, $checkOldValue=false, &$typesDestination=null, $type=null, &$oldValuesDestination=null) {
    $dbColumnValueName = str_replace('-', '_', $valueName);
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
                if ($checkOldValue) {
                    $typesDestination[$dbColumnValueName] = $type;
                }
            } else {
                $valueDestination = $value;
            }
        } else {
            global $response;
            $response['response'] = array('success'=>false, 'status'=>400, 'message'=>'Invalid '.str_replace('-', ' ', $requestVariableName).': '.$checkResult['message']);
			$noInputError = false;
            if ($checkOldValue) {
				$checkOldValue = false;
			}
        }
    }
    if ($checkOldValue) {
        if (isset($validityOptions['database']['unique']) && $validityOptions['database']['unique']) {
            $validityOptions['database']['unique'] = false;
        }
        return setRequestInputValue($oldValuesDestination, false, $valueName, 'old-'.$valueName, $inputFilters, $validityOptions);
    } else {
		return $noInputError;
	}
}

$requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
$response['response'] = array('success'=>false, 'status'=>400, 'message'=>'Invalid request: no parameters were sent.');
if (checkAuth()) {
	require_once(__ROOT__.'/lib/DbManager/DbManager.php');
	$dbManager = new DbManager();
    if (setRequestInputValue($commandName, true, $requestMethod, 'command-name', 'command-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'commands', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence', 'exceptions'=>array('login', 'logout', 'get-eatable'))))) {
		require_once(__ROOT__.'/commands.php');
        switch ($commandName) {
            case 'add-user':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!setRequestInputValue($name, true, 'POST', 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setRequestInputValue($password, true, 'password', 'password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>125))) {
                    break;
                }
                $password = password_hash($password, PASSWORD_DEFAULT);
                if (!setRequestInputValue($friendlyName, true, 'friendly-name', 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60))) {
                    break;
                }
                $response = addUser($name, $password, $friendlyName);
                break;
            case 'login':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!setRequestInputValue($userName, true, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>15))) {
                    break;
                }
                if (!setRequestInputValue($password, true, 'password', 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>255))) {
                    break;
                }
				$rememberUser = false;
				if (!setRequestInputValue($rememberUser, false, 'remember-user', 'remember-user', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
					break;
				}
                $response = login($userName, $password, $rememberUser, $appRequest);
                break;
			case 'logout':
				if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
				$response = logout($userToken);
				break;
            case 'edit-user':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $newValues = array();
                $oldValues = array();
                if (!setRequestInputValue($newValues, false, 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')), true, $typesDestination, 's', $oldValues)) {
                    break;
                }
                if (!setRequestInputValue($newValues, false, 'password', 'new-password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>125))) {
                    break;
                }
                if (isset($newValues['password'])) {
                    $newValues['password'] = password_hash($newValues['password'], PASSWORD_DEFAULT);
                }
                if (!setRequestInputValue($newValues, false, 'friendly-name', 'new-friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60), true, $typesDestination, 's', $oldValues)) {
                    break;
                }
                $response = editSnackOrUser(array('user'=>$_SESSION['user']['id']), $newValues, $typesDestination, $oldValues);
                break;
            case 'deposit':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($amount, true, 'amount', 'amount', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                $response = deposit($_SESSION['user']['id'], $amount);
                break;
            case 'add-snack':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($name, true, 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setRequestInputValue($price, true, 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                if (!setRequestInputValue($snacksPerBox, true, 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
                    break;
                }
                if (!setRequestInputValue($expirationInDays, true, 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
                    break;
                }
                if (!setRequestInputValue($isLiquid, true, 'is-liquid', 'is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
                    break;
                }
                $response = addSnack($_SESSION['user']['id'], $name, $price, $snacksPerBox, $expirationInDays, $isLiquid);
                break;
            case 'edit-snack':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($snackId, true, 'snack-id', 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                    break;
                }
                $newValues = array();
                $oldValues = array();
                $typesDestination = array();
                if (!setRequestInputValue($newValues, false, 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')), true, $typesDestination, 's', $oldValues)) {
                    break;
                }
                if (!setRequestInputValue($newValues, false, 'price', 'new-price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2), true, $typesDestination, 'd', $oldValues)) {
                    break;
                }
                if (!setRequestInputValue($newValues, false, 'snacks-per-box', 'new-snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2), true, $typesDestination, 'i', $oldValues)) {
                    break;
                }
                if (!setRequestInputValue($newValues, false, 'expiration-in-days', 'new-expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4), true, $typesDestination, 'i', $oldValues)) {
                    break;
                }
                if (!setRequestInputValue($newValues, false, 'is-liquid', 'new-is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array(), true, $typesDestination, 'i', $oldValues)) {
                    break;
                }
                $response = editSnackOrUser(array('user'=>$_SESSION['user']['id'], 'snack'=>$snackId), $newValues, $typesDestination, $oldValues);
                break;
            case 'buy':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($snackName, true, 'snack-name', 'snack-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $snackId = getIdByUniqueName('snacks', $snackName);
                if (!setRequestInputValue($quantity, true, 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
                    break;
                }
                $options = array();
                if (!setRequestInputValue($options, false, 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                if (!setRequestInputValue($options, false, 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
                    break;
                }
                if (!setRequestInputValue($options, false, 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
                    break;
                }
                $response = buy($_SESSION['user-id'], $snackId, $quantity, $options);
                break;
            case 'get-eatable':
                if (!checkrequestMethod('GET')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                $response = getEatable();
                break;
            case 'eat':
                if (!checkrequestMethod('POST')) {
                    break;
                }
                if (!checkUserToken()) {
                    break;
                }
                if (!setRequestInputValue($snackName, true, 'snack-name', 'snack-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $snackId = getIdByUniqueName('snacks', $snackName);
                $quantity = 1;
                if (!setRequestInputValue($quantity, false, 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
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
    setcookie('auth-key', null);
    setcookie('user-token', null);
	header('Content-Type: application/json');
	echo(json_encode($response));
}