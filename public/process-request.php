<?php
if (!defined('API_REQUEST')) {
    define('API_REQUEST', true);
}
if (API_REQUEST) {
    define('BASE_DIR_PATH', realpath(__DIR__.'/../').DIRECTORY_SEPARATOR);
    require BASE_DIR_PATH.'config.php';
    require BASE_DIR_PATH.'translation.php';
}
if (MAINTENANCE) {
    $response = array('success'=>true, 'status'=>503, 'message'=>getTranslatedString('response-messages', 1));
} else {
    define('REQUEST_METHOD', filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING));
    require BASE_DIR_PATH.'check-user-active.php';
    if (API_REQUEST) {
        require BASE_DIR_PATH.'check-request-method.php';
        require BASE_DIR_PATH.'check-token.php';
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
        } else if (isset($options['max-length']) && strlen($value)>$options['max-length']) {
            $valid = false;
            $message = '\''.$value.'\''.getTranslatedString('response-messages', 11).$options['max-length'].getTranslatedString('response-messages', 12);
        } else if (isset($options['greater-than']) || isset($options['less-than'])) {
            if (isset($options['greater-than']) && $value<=$options['greater-than']) {
                $valid = false;
                $message = '\''.$value.'\''.getTranslatedString('response-messages', 13).$options['greater-than'].'.';
            } else if (isset($options['less-than']) && $value>=$options['less-than']) {
                $valid = false;
                $message = '\''.$value.'\''.getTranslatedString('response-messages', 14).$options['less-than'].'.';
            }
        } else if (isset($options['timestamp']) || isset($options['date'])) {
            $format = 'Y-m-d';
            if (isset($options['timestamp'])) {
                $valueType = 'timestamp';
                $format .= ' H:i:s';
            } else {
                $valueType = 'date';
            }
            if (!$dateTime = DateTime::createFromFormat($format, $value)) {
                $valid = false;
                $message = '\''.$value.'\''.getTranslatedString('response-messages', 24);
                if (isset($options['timestamp'])) {
                    $message .= getTranslatedString('response-messages', 25);
                }
                $message .= getTranslatedString('response-messages', 26);
            }
            if ($valid && isset($options[$valueType]['greater-than']) && $dateTime<=$options[$valueType]['greater-than']) {
                $valid = false;
                $message = '\''.$value.'\''.getTranslatedString('response-messages', 13).$options[$valueType]['greater-than']->format($format).'.';
            }
            if ($valid && isset($options[$valueType]['less-than']) && $dateTime>=$options[$valueType]['less-than']) {
                $valid = false;
                $message = '\''.$value.'\''.getTranslatedString('response-messages', 14).$options[$valueType]['less-than']->format($format).'.';
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
                $dbManager->query($query, $params, $types);
                $dbValue = null;
                while ($row = $dbManager->result->fetch_assoc()) {
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
        global ${'_'.REQUEST_METHOD};
        if ($mandatory || isset(${'_'.REQUEST_METHOD}[$requestVariableName])) {
            $value = filter_input(constant('INPUT_'.REQUEST_METHOD), $requestVariableName, $inputFilterAndOptions['filter'], $filterOptions);
            if ($inputFilterAndOptions['filter']==FILTER_VALIDATE_BOOLEAN) {
                $validityOptions['boolean'] = true;
            }
            $checkResult = checkFilteredInputValidity($value, $validityOptions);
            if ($checkResult['valid']) {
                if (is_array($valueDestination)) {
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
    $response = array('success'=>false, 'status'=>400, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 23));
    if (!API_REQUEST || ($key=filter_input(INPUT_COOKIE, 'key', FILTER_SANITIZE_STRING))&&$key==API_KEY) {
        try {
            $commandName = filter_input(constant('INPUT_'.REQUEST_METHOD), 'command-name', FILTER_SANITIZE_STRING);
            $processRequestFilePath = BASE_DIR_PATH.'requests-processing/'.$commandName.'.php';
            if (is_file($processRequestFilePath)) {
                if (!isset($dbManager)) {
                    require BASE_DIR_PATH.'DbManager.php';
                    $dbManager = new DbManager();
                }
                require $processRequestFilePath;
            }
            if ((CLEAN_URLS && $commandName=filter_input(INPUT_GET, 'command-name', FILTER_SANITIZE_STRING) && $commandName=='get-main-view-data') || setRequestInputValue($commandName, true, 'command-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>25, 'database'=>array('table'=>'commands', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence', 'exceptions'=>array('login', 'logout', 'get-fund-funds', 'get-user-funds', 'get-actions', 'get-latest-actions', 'get-paginated-actions', 'get-main-view-data', 'get-user-data', 'get-snacks-data', 'get-snack-data', 'get-snack-image', 'get-to-buy', 'get-to-eat-and-user-funds'))))) {
                switch ($commandName) {
                    case 'edit-user':
                        if (API_REQUEST) {
                            if (!checkRequestMethod('POST')) {
                                break;
                            }
                            if (!checkToken()) {
                                break;
                            }
                        }
                        $dbManager->beginTransactionAndLock(array('actions'=>'w', 'edits'=>'w', 'users'=>'w'));
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
                        if (API_REQUEST) {
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
                            require BASE_DIR_PATH.'check-user-password.php';
                            if (!checkUserPassword($_SESSION['user-id'], $currentPassword)) {
                                $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('edit-user', 6));
                                break;
                            }
                        }
                        if (isset($values['password'])) {
                            $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
                            $types['password'] = 's';
                        }
                        require BASE_DIR_PATH.'commands/edit-snack-or-user.php';
                        $response = editSnackOrUser(array('user'=>$_SESSION['user-id']), $values, $types);
                        break;
                }
            }
        } catch (Exception $exception) {
            if (isset($dbManager)) {
                $dbManager->rollbackTransaction();
            }
            $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
        }
    } else {
        $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 27));
    }
}
if (API_REQUEST) {
    unset($_COOKIE['key']);
    require BASE_DIR_PATH.'set-fm-cookie.php';
    setFmCookie('key', '', time()-86400);
    if (isset($_COOKIE['token'])) {
        unset($_COOKIE['token']);
        setFmCookie('token', '', time()-86400);
    }
    if (isset($commandName) && $commandName=='get-snack-image' && !is_array($response)) {
        header('Content-Type: image/'.IMG_EXT);
    } else {
        if ($response['status']!=200) {
            http_response_code($response['status']);
        }
        header('Content-Type: application/json');
        $response = json_encode($response);
    }
    echo $response;
}
