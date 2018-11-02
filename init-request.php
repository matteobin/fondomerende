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
    else if (isset($options['greaterThan']) && $value<$options['greaterThan']) {
        $valid = false;
        $message = 'Value lesser than '.$options['greaterThan'].'.';
    }
    if ($valid && isset($options['contains'])) {
        foreach ($options['contains'] as $needle) {
            if (strpos($value, $needle)===false) {
                $valid = false;
                $message = 'Value does not contain "'.$needle.'".';
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

$commandId = filter_input(INPUT_POST, 'command-id', FILTER_SANITIZE_NUMBER_INT);
$dbManager = new DbManager();
switch ($commandId) {
    case '1':
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
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($quantity, array('greaterThan'=>0));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid quantity. '.$checkResult['message']);
            break;
        }
        $response = eat($userId, $snackId, $quantity);
        break;
    case '2':
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
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
        $checkResult = checkFilteredInputValidity($quantity, array('greaterThan'=>0));
        if (!$checkResult['valid']) {
            $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid quantity. '.$checkResult['message']);
            break;
        }
        $options = array();
        if (isset($_POST['price'])) {
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
            $checkResult = checkFilteredInputValidity($price, array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2));
            if (!$checkResult['valid']) {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid price. '.$checkResult['message']);
                break;
            } else {
                $options['price'] = $price; 
            }
        }
        if (isset($_POST['snacks_per_box'])) {
            $snacksPerBox = filter_input(INPUT_POST, 'snacks_per_box', FILTER_SANITIZE_NUMBER_INT);
            $checkResult = checkFilteredInputValidity($snacksPerBox, array('greaterThan'=>0, 'digitsNumber'=>2));
            if (!$checkResult['valid']) {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid snacks per box. '.$checkResult['message']);
                break;
            } else {
                $options['snacks_per_box'] = $snacksPerBox; 
            }
        }
        if (isset($_POST['expiration_in_days'])) {
            $expirationInDays = filter_input(INPUT_POST, 'expiration-in-days', FILTER_SANITIZE_NUMBER_INT);
            $checkResult = checkFilteredInputValidity($checkResult, array('greaterThan'=>0, 'digitsNumber'=>4));
            if (!$checkResult['valid']) {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid expiration in days. '.$checkResult['message']);
                break;
            } else {
                $options['expiration_in_days'] = $expirationInDays; 
            }
        }
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
        if isset($_POST['name']) {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $checkResult = checkFilteredInputValidity($name, array('length'=>60));
            if (!$checkResult['valid']) {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid name. '.$checkResult['message']);
                break;
            } else {
                $newValues['name'] = $name;
                $types['name'] = 's';
            }
        }
        if isset($_POST['old-name']) {
            $oldName = filter_input(INPUT_POST, 'old-name', FILTER_SANITIZE_STRING);
            $checkResult = checkFilteredInputValidity($oldName, array('length'=>60));
            if (!$checkResult['valid']) {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid old name. '.$checkResult['message']);
                break;
            } else {
                $oldValues['name'] = $oldName;
            }
        }
        if (isset($_POST['price'])) {
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
            $checkResult = checkFilteredInputValidity($price, array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2));
            if (!$checkResult['valid']) {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid price. '.$checkResult['message']);
                break;
            } else {
                $newValues['price'] = $price;
                $types['price'] = 'd';
            }
        }
        if (isset($_POST['old-price'])) {
            $oldPrice = filter_input(INPUT_POST, 'old-price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); 
            $checkResult = checkFilteredInputValidity($oldPrice, array('greaterThan'=>0, 'contains'=>array('.'), 'digitsNumber'=>4, 'decimalsNumber'=>2));
            if (!$checkResult['valid']) {
                $response = array('success'=>false, 'status'=>400, 'message'=>'Invalid old price. '.$checkResult['message']);
                break;
            } else {
                $oldValues['price'] = $oldPrice;
            }
        }
        $response = editSnackOrUser(array('user'=>$userId, 'snack'=>$snackId), $newValues, $types, $oldValues);
        break;
    case '6':
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $friendlyName = filter_input(INPUT_POST, 'friendly-name', FILTER_SANITIZE_STRING);
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