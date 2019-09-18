<?php
$appRequest = false;
if (basename($_SERVER['SCRIPT_FILENAME'])=='process-request.php') {
    $appRequest = true;
    require '../config.php';
    require '../translation.php';
}
if (MAINTENANCE) {
    $response = array('success'=>true, 'status'=>503, 'message'=>getTranslatedString('response-messages', 1));
} else {
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
            $response = array('success'=>false, 'status'=>405, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 4).$acceptedMethod.getTranslatedString('response-messages', 5));
        }
        return $requestMethodRight;
    }
    function checkUserToken() {
        $isAuth = false;
        global $userToken, $response;
        $userToken = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($userToken) && isset($_SESSION['user-token']) && $_SESSION['user-token']==$userToken) {
            $isAuth = true;
        } else {
            $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 6));
        }
        return $isAuth;
    }
    function checkUserActive() {
        $isActive = false;
        global $dbManager, $response;
        $dbManager->runPreparedQuery('SELECT active FROM users WHERE id=?', array($_SESSION['user-id']), 'i');
        if ($dbManager->getQueryRes()->fetch_assoc()['active']==1) {
            $isActive = true;
        } else {
            $response = array('success'=>true, 'status'=>401, 'message'=>getTranslatedString('response-messages', 7).getTranslatedString('response-messages', 8));
        }
        return $isActive;

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
		if (!isset($options['boolean'])) {
            $options['boolean'] = false;
        }
        if (!isset($options['can-be-empty'])) {
            $options['can-be-empty'] = false;
        }
        if (!$options['boolean'] && is_null($value)) {
            $valid = false;
            $message = getTranslatedString('response-messages', 9);
        } else if (($options['boolean'] && is_null($value)) || ((!$options['boolean'] && $value===false || $value==='') && !$options['can-be-empty'])) {
            $valid = false;
            $message = getTranslatedString('response-messages', 10);
        }
        else if (isset($options['max-length']) && strlen($value)>$options['max-length']) {
            $valid = false;
            $message = '\''.$value.'\''.getTranslatedString('response-messages', 11).$options['max-length'].getTranslatedString('response-messages', 12);
        }
        else if (isset($options['greater-than']) && $value<=$options['greater-than']) {
            $valid = false;
            $message = '\''.$value.'\''.getTranslatedString('response-messages', 13).$options['greater-than'].'.';
        } else if (isset($options['less-than']) && $value>=$options['less-than']) {
            $valid = false;
            $message = '\''.$value.'\''.getTranslatedString('response-messages', 14).$options['less-than'].'.';
        } else if (isset($options['date']['validate']) && $options['date']['validate']) {
            $unixTimestampValue = strtotime($value);
            $todayDate = date('Y-m-d');
            if ($value!=date('Y-m-d', $unixTimestampValue)) {
                $valid = false;
                //to do: add response message for invalid date
                //$message = '\''.$value.'\''.getTranslatedString('response-messages', 24);
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
                $message = '\''.$value.'\''.getTranslatedString('response-messages', 15).$options['digits-number'].'.';
            }
        }
        if ($valid && isset($options['decimals-number']) && strlen($value)-(strpos($value, '.')+1)>$options['decimals-number']) {
            $valid = false;
            $message = '\''.$value.'\''.getTranslatedString('response-messages', 16).$options['decimals-number'].'.'; 
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
                    $message = $value.''.getTranslatedString('response-messages', 17).$table.getTranslatedString('response-messages', 18).$selectColumn.getTranslatedString('response-messages', 19);
                } else if (!$insertUnique && $dbValue===null) {
                    $valid = false;
                    $message = $value.''.getTranslatedString('response-messages', 20).$table.getTranslatedString('response-messages', 18).$selectColumn.getTranslatedString('response-messages', 19);
                    if ($additionalWheres) {
                        foreach($options['database']['wheres'] as $where) {
                            $message .= getTranslatedString('response-messages', 21).$where['column'].getTranslatedString('response-messages', 22).$where['value'];
                        }
                        $message .= '.';
                    }
                } 
            }
        }
        return array('valid'=>$valid, 'message'=>$message);
    }
    function setRequestInputValue(&$valueDestination, $mandatory, $requestVariableName, array $inputFilterAndOptions, array $validityOptions) {
        $dbColumnValueName = str_replace('-', '_', $requestVariableName);
        $filterOptions = null;
        if (isset($inputFilterAndOptions['options'])) {
            $filterOptions = $inputFilterAndOptions['options'];
        }
		$noInputError = true;
        global $requestMethod, ${'_'.$requestMethod};
        if ($mandatory || isset(${'_'.$requestMethod}[$requestVariableName])) {
            $value = filter_input(constant('INPUT_'.$requestMethod), $requestVariableName, $inputFilterAndOptions['filter'], $filterOptions);
			if ($inputFilterAndOptions['filter']==FILTER_VALIDATE_BOOLEAN) {
				$validityOptions['boolean'] = true;
			}
			$checkResult = checkFilteredInputValidity($value, $validityOptions);
            if ($checkResult['valid']) {
                if (gettype($valueDestination)=='array') {
                    $valueDestination[$dbColumnValueName] = $value;
                } else {
                    $valueDestination = $value;
                }
            } else {
                global $response;
                $response = array('success'=>false, 'status'=>400, 'message'=>ucfirst(str_replace('-', ' ', $requestVariableName)).getTranslatedString('response-messages', 3).$checkResult['message']);
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
    $response = array('success'=>false, 'status'=>400, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 23));
    if (!$appRequest || checkAuth()) {
        require '../DbManager.php';
        $dbManager = new DbManager(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
        if ((FRIENDLY_URLS && isset($_GET['command-name']) && $_GET['command-name']=='get-main-view-data' && $commandName=$_GET['command-name']) || setRequestInputValue($commandName, true, 'command-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>25, 'database'=>array('table'=>'commands', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence', 'exceptions'=>array('login', 'logout', 'get-fund-funds', 'get-user-funds', 'get-actions', 'get-latest-actions', 'get-paginated-actions', 'get-main-view-data', 'get-user-data', 'get-snacks-data', 'get-snack-data', 'get-to-buy', 'get-to-eat-and-user-funds'))))) {
            switch ($commandName) {
                case 'add-user':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!setRequestInputValue($name, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>30, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                        break;
                    }
                    if (!setRequestInputValue($password, true, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
                        break;
                    }
                    if (!setRequestInputValue($friendlyName, true, 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60))) {
                        break;
                    }
                    $admin = false;
                    if (!setRequestInputValue($admin, false, 'admin', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                        break;
                    }
                    require '../commands/add-user.php';
                    $response = addUser($name, $password, $friendlyName, $admin, $appRequest);
                    break;
                case 'login':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!setRequestInputValue($userName, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>30))) {
                        break;
                    }
                    if (!setRequestInputValue($password, true, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>255))) {
                        break;
                    }
                    $rememberUser = false;
                    if (!setRequestInputValue($rememberUser, false, 'remember-user', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                        break;
                    }
                    require '../commands/login.php';
                    $response = login($userName, $password, $rememberUser, $appRequest);
                    break;
                case 'logout':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    require '../commands/logout.php';
                    $response = logout($appRequest);
                    break;
                case 'get-fund-funds':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    require '../commands/get-fund-funds.php';
                    $response = getFundFunds();
                    break;
                case 'get-user-funds':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    require '../commands/get-user-funds.php';
                    $response = getUserFunds($_SESSION['user-id']);
                    break;
                case 'get-actions':
                case 'get-latest-actions':
                case 'get-paginated-actions':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    if ($commandName=='get-latest-actions') {
                        if (!setRequestInputValue($timestamp, false, 'timestamp', array('filter'=>FILTER_SANITIZE_STRING), array())) {
                            break;
                        }
                        if (isset($timestamp)) {
                            $unixTimestamp = strtotime($timestamp);
                            if ($timestamp!=date('Y-m-d H:i:s', $unixTimestamp)) {
                                $response = array('success'=>false, 'status'=>400, 'message'=>'Timestamp'.getTranslatedString('response-messages', 3).'\''.$timestamp.'\''.getTranslatedString('response-messages', 24));
                                break;
                            }
                        } else {
                            $timestamp = date('Y-m-d H:i:s', time());
                        }
                        require '../commands/get-actions.php';
                        $response = getActions($timestamp, false, false, 'DESC');
                    } else  {
                        $timestamp = false;
                        if (!setRequestInputValue($limit, true, 'limit', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
                            break;
                        }
                        if ($commandName=='get-actions') {
                            $offset = 0;
                            if (!setRequestInputValue($offset, false, 'offset', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>-1))) {
                                break;
                            }
                        } else {
                            if (!setRequestInputValue($page, true, 'page', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
                                break;
                            }
                        }
                        $order = 'DESC';
                        if (!setRequestInputValue($ascOrder, false, 'asc-order', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                            break;
                        }
                        if ($ascOrder) {
                            $order = 'ASC';
                        }
                        if ($commandName=='get-actions' || $commandName=='get-latest-actions') {
                            require '../commands/get-actions.php';
                            $response = getActions($timestamp, $limit, $offset, $order);
                        } else {
                            require '../commands/get-paginated-actions.php';
                            $response = getPaginatedActions($limit, $page, $order);
                        }
                    }
                    break;
                case 'get-main-view-data':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
					require '../commands/get-main-view-data.php';
                    $response = getMainViewData($_SESSION['user-id']);
                    break;
                case 'get-user-data':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    require '../commands/get-user-data.php';
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
                    if (!setRequestInputValue($values, false, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>30, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique', 'exceptions'=>array($dbManager->getByUniqueId('name', 'users', $_SESSION['user-id'])))))) {
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
                            $response = array('success'=>false, 'status'=>401, 'message'=>'Wrong password!');
                            break;
                        }
                    }
                    if (isset($values['password'])) {
                        $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
                        $types['password'] = 's';
                    }
                    require '../commands/edit-snack-or-user.php';
                    $response = editSnackOrUser(array('user'=>$_SESSION['user-id']), $values, $types);
                    break;
                case 'deposit':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    if (!checkUserActive()) {
                        break;
                    }
                    if (!setRequestInputValue($amount, true, 'amount', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2))) {
                        break;
                    }
                    require '../commands/deposit.php';
                    $response = deposit($_SESSION['user-id'], $amount);
                    break;
                case 'add-snack':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    if (!checkUserActive()) {
                        break;
                    }
                    if (!setRequestInputValue($name, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
                        break;
                    }
                    if (!setRequestInputValue($price, true, 'price', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2, 'less-than'=>100))) {
                        break;
                    }
                    if (!setRequestInputValue($snacksPerBox, true, 'snacks-per-box', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'digits-number'=>3))) {
                        break;
                    }
                    if (!setRequestInputValue($expirationInDays, true, 'expiration-in-days', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'digits-number'=>4))) {
                        break;
                    }
                    $countable = true;
                    if (!setRequestInputValue($countable, false, 'countable', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                        break;
                    }
                    require '../commands/add-snack.php';
                    $response = addSnack($_SESSION['user-id'], $name, $price, $snacksPerBox, $expirationInDays, $countable);
                    break;
                case 'get-snacks-data':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    require '../commands/get-snacks-data.php';
                    $response = getSnacksData();
                    break;
                case 'get-snack-data':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    if (!setRequestInputValue($snackName, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
                        break;
                    }
                    $snackId = getIdByUniqueName('snacks', $snackName);
                    require '../commands/get-snack-data.php';
                    $response = getSnackData($snackId);
                    break;
                case 'edit-snack':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    if (!checkUserActive()) {
                        break;
                    }
                    if (!setRequestInputValue($snackId, true, 'id', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                        break;
                    }
                    $values = array();
                    if (!setRequestInputValue($values, false, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'friendly_name', 'value-type'=>'s', 'check-type'=>'insert-unique', 'exceptions'=>array($dbManager->getByUniqueId('friendly_name', 'snacks', $snackId)))))) {
                        break;
                    } else if (isset($values['name'])) {
                        $types['name'] = 's';
                        $values['friendly_name'] = $values['name'];
                        $types['friendly_name'] = 's';
                        $values['name'] = str_replace(' ', '-', strtolower($values['name']));
                    }
                    if (!setRequestInputValue($values, false, 'price', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2, 'less-than'=>100))) {
                        break;
                    } else if (isset($values['price'])) {
                        $types['price'] = 'd';
                    }
                    if (!setRequestInputValue($values, false, 'snacks-per-box', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'digits-number'=>3))) {
                        break;
                    } else if (isset($values['snacks_per_box'])) {
                        $types['snacks_per_box'] = 'i';
                    }
                    if (!setRequestInputValue($values, false, 'expiration-in-days', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'digits-number'=>4))) {
                        break;
                    } else if (isset($values['expiration_in_days'])) {
                        $types['expiration_in_days'] = 'i';
                    }
                    if (!setRequestInputValue($values, false, 'visible', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                        break;
                    } else if (isset($values['visible'])) {
                        $types['visible'] = 'i';
                    }
                    require '../commands/edit-snack-or-user.php';
                    $response = editSnackOrUser(array('user'=>$_SESSION['user-id'], 'snack'=>$snackId), $values, $types);
                    break;
                case 'get-to-buy':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    require '../commands/get-to-buy.php';
                    $response = getToBuy();
                    break;
                case 'buy':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    if (!checkUserActive()) {
                        break;
                    }
                    if (!setRequestInputValue($snackId, true, 'id', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                        break;
                    }
                    if (!setRequestInputValue($quantity, true, 'quantity', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
                        break;
                    }
                    $customiseBuyOptions = false;
                    if (!$appRequest) {
                        if (!setRequestInputValue($customiseBuyOptions, false, 'customise-buy-options', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                            break;
                        }
                    }
                    $options = array();
                    if ($appRequest || $customiseBuyOptions) {
                        if (!setRequestInputValue($options, false, 'price', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2, 'less-than'=>100))) {
                            break;
                        }
                        if (!setRequestInputValue($options, false, 'snacks-per-box', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'digits-number'=>3))) {
                            break;
                        }
                        if (!setRequestInputValue($options, false, 'expiration-in-days', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'digits-number'=>4))) {
                            break;
                        }
                        if (!setRequestInputValue($expiration, false, 'expiration', array('filter'=>FILTER_SANITIZE_STRING), array())) {
                            break;
                        }
                        if (isset($expiration)) {
                            $expirationUnixTimestamp = strtotime($expiration);
                            $todayDate = date('Y-m-d');
                            if ($expiration!=date('Y-m-d', $expirationUnixTimestamp) && $expirationUnixTimestamp<strtotime($todayDate)) {
                                $response = array('success'=>false, 'status'=>400, 'message'=>getTranslatedString('snack', 5).getTranslatedString('response-messages', 3).'\''.$expiration.'\''.getTranslatedString('response-messages', 24));
                                break;
                            } else {
                                $options['expiration_in_days'] = ((new DateTime($todayDate))->diff(new DateTime($expiration)))->d;
                            }
                        }
                    }
                    require '../commands/buy.php';
                    $response = buy($_SESSION['user-id'], $snackId, $quantity, $options);
                    break;
                case 'get-to-eat-and-user-funds':
                    if (!checkRequestMethod('GET')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    require '../commands/get-to-eat-and-user-funds.php';
                    $response = getToEatAndUserFunds($_SESSION['user-id']);
                    break;
                case 'eat':
                    if (!checkRequestMethod('POST')) {
                        break;
                    }
                    if (!checkUserToken()) {
                        break;
                    }
                    if (!checkUserActive()) {
                        break;
                    }
                    if (!setRequestInputValue($snackId, true, 'id', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
                        break;
                    }
                    $quantity = 1;
                    if (!setRequestInputValue($quantity, false, 'quantity', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
                        break;
                    }
                    require '../commands/eat.php';
                    $response = eat($_SESSION['user-id'], $snackId, $quantity);
                    break;
            }
        }
    } else {
        $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 25));
    }
}
if ($appRequest) {
    unset($_COOKIE['auth-key']);
    unset($_COOKIE['user-token']);
    setcookie('auth-key', '', time()-3600);
    setcookie('user-token', '', time()-3600);
    if ($response['status']!=200) {
        http_response_code($response['status']);
    }
    header('Content-Type: application/json');
    echo json_encode($response);
}
