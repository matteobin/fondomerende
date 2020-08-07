<?php
if (!defined('API_REQUEST')) {
    define('API_REQUEST', true);
}
if (API_REQUEST) {
    define('BASE_DIR_PATH', realpath(__DIR__.'/../').DIRECTORY_SEPARATOR);
    define('FUNCTIONS_PATH', BASE_DIR_PATH.'functions'.DIRECTORY_SEPARATOR);
    require BASE_DIR_PATH.'config.php';
    session_start();
    require FUNCTIONS_PATH.'get-translated-string.php';
}
if (MAINTENANCE) {
    $response = array('success'=>true, 'status'=>503, 'message'=>getTranslatedString('response-messages', 1));
} else {
    define('COMMANDS_PATH', FUNCTIONS_PATH.'commands'.DIRECTORY_SEPARATOR);
    if (API_REQUEST) {
        define('INJECTIONS_PATH', BASE_DIR_PATH.'injections'.DIRECTORY_SEPARATOR);
    }
    define('REQUEST_METHOD', filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING));
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
            require INJECTIONS_PATH.'timestamp-or-date-check.php';
        }
        if ($valid && isset($options['digits-number'])) {
            require INJECTIONS_PATH.'digits-number-check.php';
        }
        if ($valid && isset($options['decimals-number']) && strlen($value)-(strpos($value, '.')+1)>$options['decimals-number']) {
            $valid = false;
            $message = '\''.$value.'\''.getTranslatedString('response-messages', 16).$options['decimals-number'].'.'; 
        }
        if ($valid && isset($options['database'])) {
            require INJECTIONS_PATH.'database-check.php';
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
    if (!API_REQUEST || filter_input(INPUT_SERVER, 'API-Key', FILTER_SANITIZE_STRING)==API_KEY) {
        try {
            $commandName = filter_input(constant('INPUT_'.REQUEST_METHOD), 'command-name', FILTER_SANITIZE_STRING);
            $processRequestFilePath = INJECTIONS_PATH.'requests'.DIRECTORY_SEPARATOR.$commandName.'.php';
            if (is_file($processRequestFilePath)) {
                if (!isset($dbManager)) {
                    require BASE_DIR_PATH.'DbManager.php';
                    $dbManager = new DbManager();
                }
                require $processRequestFilePath;
            }
        } catch (Exception $e) {
            if (isset($dbManager)) {
                $dbManager->rollbackTransaction();
            }
            $response = array('success'=>false, 'status'=>500, 'message'=>$e->getMessage());
        }
    } else {
        $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 27));
    }
}
if (API_REQUEST) {
    require INJECTIONS_PATH.'api-request-echo.php';
}
