<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/lib/DbManager/DbManager.php');
require_once(__ROOT__.'/commands.php');

function checkFilteredInputValidity($value, $options=null) {
    $valid = true;
    $message = '';
    if ($value=='') {
        $valid = false;
        $message = 'Value in wrong format.';
    }
    else if (isset($options['length']) && strlen($value)>$options['length']) {
        $valid = false;
        $message = 'Value longer than '.$options['length'].' characters.';
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

function setInputValue(&$destination, $mandatory, $requestType, $valueName, $inputVariableName, array $inputFilters, array $validityOptions, $checkOldValue=false, &$types=null, $type=null, &$oldValues=null) {
    global $response;
    $requestType = mb_strtoupper($requestType);
    $dbColumnValueName = str_replace('-', '_', $valueName);
    $filter = $inputFilters['filter'];
    $filterOptions = null;
    $valueSet = false;
    if (isset($inputFilters['options'])) {
        $filterOptions = $inputFilters['options'];
    }
	global ${'_'.$requestType};
    if ($mandatory || isset(${'_'.$requestType}[$inputVariableName])) {
        $value = filter_input(constant('INPUT_'.$requestType), $inputVariableName, $filter, $filterOptions);
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
			$valueSet = true;
        } else {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid '.str_replace('-', ' ', $inputVariableName).'. '.$checkResult['message']);
			if ($checkOldValue) {
				$checkOldValue = false;
			}
        }
    }
    if ($checkOldValue) {
        setInputValue($oldValues, false, $requestType, $valueName, 'old-'.$valueName, $inputFilters, $validityOptions);
    } else {
		return $valueSet;
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
        if (!setInputValue($options, false, 'post', 'price', 'price', array('filter'=>FILTER_SANITIZE_NUMBER_INT, 'options'=>FILTER_FLAG_ALLOW_FRACTION), array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2))) {
			break;
		}
        if (!setInputValue($options, false, 'post', 'snacks-per-box', 'snacks-per-box', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>2))) {
			break;
		}
        if (!setInputValue($options, false, 'post', 'expiration-in-days', 'expiration-in-days', array('filter'=>FILTER_SANITIZE_NUMBER_INT), array('greaterThan'=>0, 'digitsNumber'=>4))) {
			break;
		}
        var_dump($options);
        die();
        $response = buy($userId, $snackId, $quantity, $options);
        break;
    case '3':
        $userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($userId, array('dbCheck'=>array('table'=>'users', 'column'=>'id')));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid user id. '.$checkResult['message']);
            break;
        }
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $checkResult = checkFilteredInputValidity($amount, array('greaterThan'=>0, 'digitsNumber'=>4, 'decimalsNumber'=>2));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid amount. '.$checkResult['message']);
            break;
        }
        var_dump(deposit($userId, $amount));
        break;
    case '4':
        $userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($userId, array('dbCheck'=>array('table'=>'users', 'column'=>'id')));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid user id. '.$checkResult['message']);
            break;
        }
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $checkResult = checkFilteredInputValidity($name, array('length'=>60));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid name. '.$checkResult['message']);
            break;
        }
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
        $checkResult = checkFilteredInputValidity($price, array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid price. '.$checkResult['message']);
            break;
        }
        $snacksPerBox = filter_input(INPUT_POST, 'snacks-per-box', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($snacksPerBox, array('greaterThan'=>0, 'digitsNumber'=>2));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid snacks per box. '.$checkResult['message']);
            break;
        }
        $expirationInDays = filter_input(INPUT_POST, 'expiration-in-days', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($expirationInDays, array('greaterThan'=>0, 'digitsNumber'=>4));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid expiration in days. '.$checkResult['message']);
            break;
        }
        $isLiquid = filter_input(INPUT_POST, 'is-liquid', FILTER_VALIDATE_BOOLEAN);
        $checkResult = checkFilteredInputValidity($isLiquid);
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid is liquid. '.$checkResult['message']);
            break;
        }
        var_dump(addSnack($userId, $name, $price, $snacksPerBox, $expirationInDays, $isLiquid));
        break;
    case '5':
        $userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($userId, array('dbCheck'=>array('table'=>'users', 'column'=>'id')));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid user id. '.$checkResult['message']);
            break;
        }
        $snackId = filter_input(INPUT_POST, 'snack-id', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($snackId, array('dbCheck'=>array('table'=>'snacks', 'column'=>'id')));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid snack id. '.$checkResult['message']);
            break;
        }
        $newValues = array();
        $oldValues = array();
        $types = array();
        if (isset($_POST['new-name'])) {
            $name = filter_input(INPUT_POST, 'new-name', FILTER_SANITIZE_STRING);
            $checkResult = checkFilteredInputValidity($name, array('length'=>60));
            if ($checkResult['valid']) {
                $newValues['name'] = $name;
                $types['name'] = 's';
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid new name. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['old-name'])) {
            $name = filter_input(INPUT_POST, 'old-name', FILTER_SANITIZE_STRING);
            $checkResult = checkFilteredInputValidity($name, array('length'=>60));
            if ($checkResult['valid']) {
                $oldValues['name'] = $name;
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid old name. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['new-price'])) {
            $price = filter_input(INPUT_POST, 'new-price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
            $checkResult = checkFilteredInputValidity($price, array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2));
            if ($checkResult['valid']) {
                $newValues['price'] = $price;
                $types['price'] = 'd';
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid new price. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['old-price'])) {
            $price = filter_input(INPUT_POST, 'old-price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
            $checkResult = checkFilteredInputValidity($price, array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2));
            if ($checkResult['valid']) {
                $oldValues['price'] = $price;
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid old price. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['new-snacks-per-box'])) {
            $snacksPerBox = filter_input(INPUT_POST, 'new-snacks-per-box', FILTER_SANITIZE_NUMBER_INT);
            $checkResult = checkFilteredInputValidity($snacksPerBox, array('greaterThan'=>0, 'digitsNumber'=>2));
            if ($checkResult['valid']) {
                $newValues['snacks_per_box'] = $snacksPerBox;
                $types['snacks_per_box'] = 'i';
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid new snacks per box. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['old-snacks-per-box'])) {
            $snacksPerBox = filter_input(INPUT_POST, 'old-snacks-per-box', FILTER_SANITIZE_NUMBER_INT);
            $checkResult = checkFilteredInputValidity($snacksPerBox, array('greaterThan'=>0, 'digitsNumber'=>2));
            if ($checkResult['valid']) {
                $oldValues['snacks_per_box'] = $snacksPerBox;
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid old snacks per box. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['new-expiration-in-days'])) {
            $expirationInDays = filter_input(INPUT_POST, 'new-expiration-in-days', FILTER_SANITIZE_NUMBER_INT);
            $checkResult = checkFilteredInputValidity($expirationInDays, array('greaterThan'=>0, 'digitsNumber'=>4));
            if ($checkResult['valid']) {
                $newValues['expiration_in_days'] = $expirationInDays;
                $types['expiration_in_days'] = 'i';
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid new expiration in days. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['old-expiration-in-days'])) {
            $expirationInDays = filter_input(INPUT_POST, 'old-expiration-in-days', FILTER_SANITIZE_NUMBER_INT);
            $checkResult = checkFilteredInputValidity($expirationInDays, array('greaterThan'=>0, 'digitsNumber'=>4));
            if ($checkResult['valid']) {
                $oldValues['expiration_in_days'] = $expirationInDays;
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid old expiration in days. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['new-is-liquid'])) {
            $isLiquid = filter_input(INPUT_POST, 'new-is-liquid', FILTER_VALIDATE_BOOLEAN);
            $checkResult = checkFilteredInputValidity($isLiquid);
            if ($checkResult['valid']) {
                $newValues['is_liquid'] = $isLiquid;
                $types['is_liquid'] = 'i';
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid new is liquid. '.$checkResult['message']);
                break;
            }
        }
        if (isset($_POST['old-is-liquid'])) {
            $isLiquid = filter_input(INPUT_POST, 'old-is-liquid', FILTER_VALIDATE_BOOLEAN);
            $checkResult = checkFilteredInputValidity($isLiquid);
            if ($checkResult['valid']) {
                $oldValues['is_liquid'] = $isLiquid;
            } else {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid old is liquid. '.$checkResult['message']);
                break;
            }
        }
        $response = editSnackOrUser(array('user'=>$userId, 'snack'=>$snackId), $newValues, $types, $oldValues);
        break;
    case '6':
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $checkResult = checkFilteredInputValidity($name, array('length'=>15));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid name. '.$checkResult['message']);
            break;
        }
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $checkResult = checkFilteredInputValidity($password, array('length'=>60));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid password. '.$checkResult['message']);
            break;
        }
        $friendlyName = filter_input(INPUT_POST, 'friendly-name', FILTER_SANITIZE_STRING);
        $checkResult = checkFilteredInputValidity($friendlyName, array('length'=>60));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid friendly name. '.$checkResult['message']);
            break;
        }
        var_dump(addUser($name, $password, $friendlyName));
        break;
    case '7':
        $userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        var_dump(editSnackOrUser(array('user'=>$userId), array('name'=>$name), array('name'=>'s'), array('name'=>'pk9rocco')));
        break;
}
header('Content-Type: application/json');
echo(json_encode($response));