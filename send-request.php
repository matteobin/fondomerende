<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/auth-key.php');

function checkAuth() {
    $isAuth = false;
    $authKey = filter_input(INPUT_COOKIE, 'auth-key', FILTER_SANITIZE_STRING); 
    if ($authKey==AUTH_KEY) {
        $isAuth = true;
    }
    return $isAuth;
}


function checkRequestType($acceptedType) {
    $acceptedType = strtoupper($acceptedType);
    $requestTypeRight = true;
    global $requestType;
    if ($requestType!=$acceptedType) {
        $requestTypeRight = false;
        global $response;
        $response = array('success'=>false, 'status'=>405, 'message'=>'Invalid request: you should send it through '.$acceptedType.' instead.');
    }
    return $requestTypeRight;
}

function checkCustomerToken() {
    $isAuth = false;
    global $userToken;
    $userToken = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
    session_start();
    if (isset($_SESSION['users'][$userToken])) {
        $isAuth = true;
    } else {
        global $response;
        $response['response'] = array('success'=>false, 'status'=>401, 'message'=>'Invalid customer token: missing or expired.');
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
            } else if ($dbValue===null) {
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

function setRequestValue(&$destination, $mandatory, $requestType, $valueName, $requestVariableName, array $inputFilters, array $validityOptions, $checkOldValue=false, &$types=null, $type=null, &$oldValues=null) {
    $dbColumnValueName = str_replace('-', '_', $valueName);
    $filter = $inputFilters['filter'];
    $filterOptions = null;
    $noInputError = true;
    if (isset($inputFilters['options'])) {
        $filterOptions = $inputFilters['options'];
    }
	global ${'_'.$requestType};
    if ($mandatory || isset(${'_'.$requestType}[$requestVariableName])) {
        $value = filter_input(constant('INPUT_'.$requestType), $requestVariableName, $filter, $filterOptions);
        $checkResult = checkFilteredInputValidity($value, $validityOptions);
        if ($checkResult['valid']) {
            if (gettype($destination)=='array') {
                $destination[$dbColumnValueName] = $value;
                if ($checkOldValue) {
                    $types[$dbColumnValueName] = $type;
                }
            } else {
                $destination = $value;
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
        return setRequestValue($oldValues, false, $requestType, $valueName, 'old-'.$valueName, $inputFilters, $validityOptions);
    } else {
		return $noInputError;
	}
}

$requestType = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
$response['response'] = array('success'=>false, 'status'=>400, 'message'=>'Invalid request: no parameters were sent.');
if (checkAuth()) {
	require_once(__ROOT__.'/lib/DbManager/DbManager.php');
	$dbManager = new DbManager();
    if (setRequestValue($commandName, true, $requestType, 'command-name', 'command-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'commands', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence', 'exceptions'=>array('login'))))) {
		require_once(__ROOT__.'/commands.php');
        switch ($commandName) {
            case 'add-user':
                if (!checkRequestType('POST')) {
                    break;
                }
                if (!setRequestValue($name, true, 'POST', 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setRequestValue($password, true, 'POST', 'password', 'password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>125))) {
                    break;
                }
                $password = password_hash($password, PASSWORD_DEFAULT);
                if (!setRequestValue($friendlyName, true, 'POST', 'friendly-name', 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60))) {
                    break;
                }
                $response = addUser($name, $password, $friendlyName);
                break;
            case 'login':
                if (!checkRequestType('POST')) {
                    break;
                }
                if (!setRequestValue($userName, true, 'POST', 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>15))) {
                    break;
                }
                if (!setRequestValue($password, true, 'POST', 'password', 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>255))) {
                    break;
                }
                $response = login($userName, $password);
                break;
            case 'edit-user':
                if (!checkRequestType('POST')) {
                    break;
                }
                if (!checkCustomerToken()) {
                    break;
                }
                $newValues = array();
                $oldValues = array();
                if (!setRequestValue($newValues, false, 'POST', 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')), true, $types, 's', $oldValues)) {
                    break;
                }
                if (!setRequestValue($newValues, false, 'POST', 'password', 'new-password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>125))) {
                    break;
                }
                if (isset($newValues['password'])) {
                    $newValues['password'] = password_hash($newValues['password'], PASSWORD_DEFAULT);
                }
                if (!setRequestValue($newValues, false, 'POST', 'friendly-name', 'new-friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60), true, $types, 's', $oldValues)) {
                    break;
                }
                $response = editSnackOrUser(array('user'=>$_SESSION['users'][$userToken]['id']), $newValues, $types, $oldValues);
                break;
            case 'deposit':
                if (!checkRequestType('POST')) {
                    break;
                }
                if (!checkCustomerToken()) {
                    break;
                }
                if (!setRequestValue($amount, true, 'POST', 'amount', 'amount', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                $response = deposit($_SESSION['users'][$userToken]['id'], $amount);
                break;
            case 'add-snack':
                if (!checkCustomerToken()) {
                    break;
                }
                if (!setRequestValue($name, true, 'POST', 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setRequestValue($price, true, 'POST', 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                if (!setRequestValue($snacksPerBox, true, 'POST', 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
                    break;
                }
                if (!setRequestValue($expirationInDays, true, 'POST', 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
                    break;
                }
                if (!setRequestValue($isLiquid, true, 'POST', 'is-liquid', 'is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
                    break;
                }
                $response = addSnack($_SESSION['users'][$userToken]['id'], $name, $price, $snacksPerBox, $expirationInDays, $isLiquid);
                break;
            case 'edit-snack':
                if (!checkRequestType('POST')) {
                    break;
                }
                if (!checkCustomerToken()) {
                    break;
                }
                if (!setRequestValue($snackId, true, 'POST', 'snack-id', 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                    break;
                }
                $newValues = array();
                $oldValues = array();
                $types = array();
                if (!setRequestValue($newValues, false, 'POST', 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')), true, $types, 's', $oldValues)) {
                    break;
                }
                if (!setRequestValue($newValues, false, 'POST', 'price', 'new-price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2), true, $types, 'd', $oldValues)) {
                    break;
                }
                if (!setRequestValue($newValues, false, 'POST', 'snacks-per-box', 'new-snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2), true, $types, 'i', $oldValues)) {
                    break;
                }
                if (!setRequestValue($newValues, false, 'POST', 'expiration-in-days', 'new-expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4), true, $types, 'i', $oldValues)) {
                    break;
                }
                if (!setRequestValue($newValues, false, 'POST', 'is-liquid', 'new-is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array(), true, $types, 'i', $oldValues)) {
                    break;
                }
                $response = editSnackOrUser(array('user'=>$_SESSION['users'][$userToken]['id'], 'snack'=>$snackId), $newValues, $types, $oldValues);
                break;
            case 'buy':
                if (!checkRequestType('POST')) {
                    break;
                }
                if (!checkCustomerToken()) {
                    break;
                }
                if (!setRequestValue($snackName, true, 'POST', 'snack-name', 'snack-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $snackId = getIdByUniqueName('snacks', $snackName);
                if (!setRequestValue($quantity, true, 'POST', 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
                    break;
                }
                $options = array();
                if (!setRequestValue($options, false, 'POST', 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                if (!setRequestValue($options, false, 'POST', 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
                    break;
                }
                if (!setRequestValue($options, false, 'POST', 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
                    break;
                }
                $response = buy($_SESSION['users'][$userToken]['id'], $snackId, $quantity, $options);
                break;
            case 'eat':
                if (!checkRequestType('POST')) {
                    break;
                }
                if (!checkCustomerToken()) {
                    break;
                }
                if (!setRequestValue($snackName, true, 'POST', 'snack-name', 'snack-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $snackId = getIdByUniqueName('snacks', $snackName);
                $quantity = 1;
                if (!setRequestValue($quantity, false, 'POST', 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
                    break;
                }
                $response = eat($_SESSION['users'][$userToken]['id'], $snackId, $quantity);
                break;
        }
    }
} else {
    $response['response'] = array('success'=>false, 'status'=>401, 'message'=>'Invalid request: missing or wrong auth key.');
}
header('Content-Type: application/json');
echo(json_encode($response));