<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/auth-key.php');

function authRequest($requestType) {
    $isAuth = false;
    $authKey = filter_input(constant('INPUT_'.$requestType), 'auth-key', FILTER_SANITIZE_STRING);
    if ($authKey==AUTH_KEY) {
        $isAuth = true;
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
    if ($value==null) {
        $valid = false;
        $message = 'value missing.';
    } else if ($value==='') {
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
            if (isset($options['database']['where-column'])) {
                $whereColumn = $options['database']['where-column'];
                $query .= ' AND '.$whereColumn.'=?';
                $whereValue = $options['database']['where-value'];
                $params[] = $whereValue;
                $types .= $valueType;
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
                $message = '\''.$value.'\' is already present in database '.$table.' table at '.$whereColumn.' column.';
            } else if ($dbValue===null) {
                $valid = false;
                $message = '\''.$value.'\' is not present in database '.$table.' table at '.$selectColumn.' column';
                if (isset($whereColumn)) {
                    $message .= ', where '.$whereColumn.' column is \''.$whereValue.'\'';
                }
                $message .= '.';
            } 
        }
    }
    return array('valid'=>$valid, 'message'=>$message);
}

function setInputValue(&$destination, $mandatory, $requestType, $valueName, $requestVariableName, array $inputFilters, array $validityOptions, $checkOldValue=false, &$types=null, $type=null, &$oldValues=null) {
    global $response;
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
        return setInputValue($oldValues, false, $requestType, $valueName, 'old-'.$valueName, $inputFilters, $validityOptions);
    } else {
		return $noInputError;
	}
}

$requestType = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
$response['response'] = array('success'=>false, 'status'=>400, 'message'=>'Invalid request. No parameters were sent.');
if (authRequest($requestType)) {
	require_once(__ROOT__.'/lib/DbManager/DbManager.php');
	$dbManager = new DbManager();
    if (setInputValue($commandName, true, $requestType, 'command-name', 'command-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'commands', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
		require_once(__ROOT__.'/commands.php');
        switch ($commandName) {
            case 'login':
                if (!setInputValue($userName, true, $requestType, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array())) {
                    break;
                }
                if (!setInputValue($password, true, $requestType, 'password', 'password', array('filter'=>FILTER_SANITIZE_STRING), array())) {
                    break;
                }
                $response = login($userName, $password);
                break;
            case 'eat':
                if (!setInputValue($userName, true, $requestType, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $userId = getIdByUniqueName('users', $userName);
                if (!setInputValue($snackName, true, $requestType, 'snack-name', 'snack-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $snackId = getIdByUniqueName('snacks', $snackName);
                if (!setInputValue($quantity, true, $requestType, 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
                    break;
                }
                $response = eat($userId, $snackId, $quantity);
                break;
            case 'buy':
                if (!setInputValue($userName, true, $requestType, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $userId = getIdByUniqueName('users', $userName);
                if (!setInputValue($snackName, true, $requestType, 'snack-name', 'snack-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $snackId = getIdByUniqueName('snacks', $snackName);
                if (!setInputValue($quantity, true, $requestType, 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
                    break;
                }
                $options = array();
                if (!setInputValue($options, false, $requestType, 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                if (!setInputValue($options, false, $requestType, 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
                    break;
                }
                if (!setInputValue($options, false, $requestType, 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
                    break;
                }
                $response = buy($userId, $snackId, $quantity, $options);
                break;
            case 'deposit':
                if (!setInputValue($userName, true, $requestType, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $userId = getIdByUniqueName('users', $userName);
                if (!setInputValue($amount, true, $requestType, 'amount', 'amount', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                $response = deposit($userId, $amount);
                break;
            case 'add snack':
                if (!setInputValue($userName, true, $requestType, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $userId = getIdByUniqueName('users', $userName);
                if (!setInputValue($name, true, $requestType, 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setInputValue($price, true, $requestType, 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
                    break;
                }
                if (!setInputValue($snacksPerBox, true, $requestType, 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
                    break;
                }
                if (!setInputValue($expirationInDays, true, $requestType, 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
                    break;
                }
                if (!setInputValue($isLiquid, true, $requestType, 'is-liquid', 'is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
                    break;
                }
                $response = addSnack($userId, $name, $price, $snacksPerBox, $expirationInDays, $isLiquid);
                break;
            case 'edit snack':
                if (!setInputValue($userName, true, $requestType, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $userId = getIdByUniqueName('users', $userName);
                if (!setInputValue($snackId, true, $requestType, 'snack-id', 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                    break;
                }
                $newValues = array();
                $oldValues = array();
                $types = array();
                if (!setInputValue($newValues, false, $requestType, 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')), true, $types, 's', $oldValues)) {
                    break;
                }
                if (!setInputValue($newValues, false, $requestType, 'price', 'new-price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2), true, $types, 'd', $oldValues)) {
                    break;
                }
                if (!setInputValue($newValues, false, $requestType, 'snacks-per-box', 'new-snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2), true, $types, 'i', $oldValues)) {
                    break;
                }
                if (!setInputValue($newValues, false, $requestType, 'expiration-in-days', 'new-expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4), true, $types, 'i', $oldValues)) {
                    break;
                }
                if (!setInputValue($newValues, false, $requestType, 'is-liquid', 'new-is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array(), true, $types, 'i', $oldValues)) {
                    break;
                }
                $response = editSnackOrUser(array('user'=>$userId, 'snack'=>$snackId), $newValues, $types, $oldValues);
                break;
            case 'add user':
                if (!setInputValue($name, true, $requestType, 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                    break;
                }
                if (!setInputValue($password, true, $requestType, 'password', 'password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>125))) {
                    break;
                }
                $password = password_hash($password, PASSWORD_DEFAULT);
                if (!setInputValue($friendlyName, true, $requestType, 'friendly-name', 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60))) {
                    break;
                }
                $response = addUser($name, $password, $friendlyName);
                break;
            case 'edit user':
                if (!setInputValue($userName, true, $requestType, 'user-name', 'user-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                    break;
                }
                $userId = getIdByUniqueName('users', $userName);
                $newValues = array();
                $oldValues = array();
                if (!setInputValue($newValues, false, $requestType, 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')), true, $types, 's', $oldValues)) {
                    break;
                }
                if (!setInputValue($newValues, false, $requestType, 'password', 'new-password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>125))) {
                    break;
                }
                if (isset($newValues['password'])) {
                    $newValues['password'] = password_hash($newValues['password'], PASSWORD_DEFAULT);
                }
                if (!setInputValue($newValues, false, $requestType, 'friendly-name', 'new-friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60), true, $types, 's', $oldValues)) {
                    break;
                }
                $response = editSnackOrUser(array('user'=>$userId), $newValues, $types, $oldValues);
                break;
        }
    }
} else {
    $response['response'] = array('success'=>false, 'status'=>401, 'message'=>'Invalid request. Missing or wrong auth key.');
}
header('Content-Type: application/json');
echo(json_encode($response));