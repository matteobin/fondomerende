<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/lib/DbManager/DbManager.php');
require_once(__ROOT__.'/commands.php');

function checkFilteredInputValidity($value, $options=null) {
    $valid = true;
    $message = '';
    if ($value==='') {
        $valid = false;
        $message = 'Value in wrong format.';
    }
    else if (isset($options['maxLength']) && strlen($value)>$options['maxLength']) {
        $valid = false;
        $message = 'Value longer than '.$options['maxLength'].' characters.';
    }
    else if (isset($options['greaterThan']) && $value<=$options['greaterThan']) {
        $valid = false;
        $message = 'Value lesser than '.($options['greaterThan']+1).'.';
    }
    if ($valid && isset($options['contains'])) {
        foreach ($options['contains'] as $needle) {
            if (strpos($value, $needle)===false) {
                $valid = false;
                $message = 'Value does not contain \''.$needle.'\'.';
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
            $message = 'Value has more digits than '.$options['digitsNumber'].'.';
        }
    }
    if ($valid && isset($options['decimalsNumber']) && strlen($value)-(strpos($value, '.')+1)>$options['decimalsNumber']) {
        $valid = false;
        $message = 'Value has more decimals than '.$options['decimalsNumber'].'.'; 
    }
    if ($valid && isset($options['dbCheck'])) {
        global $dbManager;
        $column = $options['dbCheck']['column'];
        $table = $options['dbCheck']['table'];
        $dbManager->runPreparedQuery('SELECT '.$column.' FROM '.$table.' WHERE '.$column.'=?', array($value), 'i');
        $dbValue = null;
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $dbValue = $row[$column];
        }
        if ($dbValue!=$value) {
            $valid = false;
            $message = 'Value is not present in database '.$table.' table at '.$column.' column.';
        }
    }
    return array('valid'=>$valid, 'message'=>$message);
}

function setInputValue(&$destination, $mandatory, $requestType, $valueName, $requestVariableName, array $inputFilters, array $validityOptions, $checkOldValue=false, &$types=null, $type=null, &$oldValues=null) {
    global $response;
    $requestType = mb_strtoupper($requestType);
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
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid '.str_replace('-', ' ', $requestVariableName).'. '.$checkResult['message']);
			$noInputError = false;
            if ($checkOldValue) {
				$checkOldValue = false;
			}
        }
    }
    if ($checkOldValue) {
        return setInputValue($oldValues, false, $requestType, $valueName, 'old-'.$valueName, $inputFilters, $validityOptions);
    } else {
		return $noInputError;
	}
}

$commandId = filter_input(INPUT_POST, 'command-id', FILTER_SANITIZE_NUMBER_INT);
$dbManager = new DbManager();
switch ($commandId) {
    case '1':
        if (!setInputValue($userId, true, 'post', 'user-id', 'user-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'users', 'column'=>'id')))) {
			break;
		}
        if (!setInputValue($snackId, true, 'post', 'snack-id', 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'snacks', 'column'=>'id')))) {
			break;
		}
		if (!setInputValue($quantity, true, 'post', 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
			break;
		}
        $response = eat($userId, $snackId, $quantity);
        break;
    case '2':
        if (!setInputValue($userId, true, 'post', 'user-id', 'user-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'users', 'column'=>'id')))) {
			break;
		}
        if (!setInputValue($snackId, true, 'post', 'snack-id', 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'snacks', 'column'=>'id')))) {
			break;
		}
        if (!setInputValue($quantity, true, 'post', 'quantity', 'quantity', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0))) {
			break;
		}
        $options = array();
        if (!setInputValue($options, false, 'post', 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
			break;
		}
        if (!setInputValue($options, false, 'post', 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
			break;
		}
        if (!setInputValue($options, false, 'post', 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
			break;
		}
        $response = buy($userId, $snackId, $quantity, $options);
        break;
    case '3':
        if (!setInputValue($userId, true, 'post', 'user-id', 'user-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'users', 'column'=>'id')))) {
			break;
		}
        if (!setInputValue($amount, true, 'post', 'amount', 'amount', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
			break;
		}
        $response = deposit($userId, $amount);
        break;
    case '4':
        if (!setInputValue($userId, true, 'post', 'user-id', 'user-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'users', 'column'=>'id')))) {
			break;
		}
        if (!setInputValue($name, true, 'post', 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60))) {
			break;
		}
        if (!setInputValue($price, true, 'post', 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
			break;
		}
        if (!setInputValue($snacksPerBox, true, 'post', 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
			break;
		}
        if (!setInputValue($expirationInDays, true, 'post', 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
			break;
		}
        if (!setInputValue($isLiquid, true, 'post', 'is-liquid', 'is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array())) {
			break;
		}
        $response = addSnack($userId, $name, $price, $snacksPerBox, $expirationInDays, $isLiquid);
        break;
    case '5':
        if (!setInputValue($userId, true, 'post', 'user-id', 'user-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'users', 'column'=>'id')))) {
			break;
		}
        if (!setInputValue($snackId, true, 'post', 'snack-id', 'snack-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'snacks', 'column'=>'id')))) {
			break;
		}
        $newValues = array();
        $oldValues = array();
        $types = array();
        if (!setInputValue($newValues, false, 'post', 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60), true, $types, 's', $oldValues)) {
			break;
		}
        if (!setInputValue($newValues, false, 'post', 'price', 'new-price', array('filter'=>FILTER_SANITIZE_NUMBER_FLOAT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2), true, $types, 'd', $oldValues)) {
			break;
		}
        if (!setInputValue($newValues, false, 'post', 'snacks-per-box', 'new-snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2), true, $types, 'i', $oldValues)) {
			break;
		}
        if (!setInputValue($newValues, false, 'post', 'expiration-in-days', 'new-expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4), true, $types, 'i', $oldValues)) {
			break;
		}
        if (!setInputValue($newValues, false, 'post', 'is-liquid', 'new-is-liquid', array('filter'=>FILTER_VALIDATE_BOOLEAN), array(), true, $types, 'i', $oldValues)) {
			break;
		}
        $response = editSnackOrUser(array('user'=>$userId, 'snack'=>$snackId), $newValues, $types, $oldValues);
        break;
    case '6':
        if (!setInputValue($name, true, 'post', 'name', 'name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15))) {
			break;
		}
        if (!setInputValue($password, true, 'post', 'password', 'password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60))) {
			break;
		}
        if (!setInputValue($friendlyName, true, 'post', 'friendly-name', 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60))) {
			break;
		}
        $response = addUser($name, $password, $friendlyName);
        break;
    case '7':
        if (!setInputValue($userId, true, 'post', 'user-id', 'user-id', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'dbCheck'=>array('table'=>'users', 'column'=>'id')))) {
			break;
		}
        $newValues = array();
        $oldValues = array();
        if (!setInputValue($newValues, false, 'post', 'name', 'new-name', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>15), true, $types, 's', $oldValues)) {
			break;
		}
        if (!setInputValue($oldValues, false, 'post', 'password', 'old-password', array('filter'=>FILTER_SANITIZE_STRING), array('maxLength'=>60), true, $types, 's', $oldValues)) {
			break;
		}
        $response = editSnackOrUser(array('user'=>$userId), $newValues, $types, $oldValues);
        break;
}
header('Content-Type: application/json');
echo(json_encode($response));