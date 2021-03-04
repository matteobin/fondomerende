<?php
function getFormat($row) {
    global $formats;
    if (!isset($formats)) {
        $formats = array();
    }
    $code = filter_input(INPUT_GET, 'format', FILTER_SANITIZE_STRING);
    if ($code) {
        $_SESSION['format'] = $code;
    } else if (isset($_SESSION['format'])) {
        $code = $_SESSION['format'];
    } else {
        $code = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING);
        $code = $code ? strtolower(substr($code, 3, 2)) : 'gb';
        $_SESSION['format'] = $code;
    }
    $index = $row-1;
    $key = 'fm-'.$_SESSION['format'].'-format';
    if (isset($formats[$code], $formats[$code][$index])) {
        $format = $formats[$code][$index];
    } else if (APCU_ENABLED && apcu_exists($key) && ($formatRows=apcu_fetch($key)) && isset($formatRows[$index])) {
        $format = $formatRows[$index];
        if (!isset($formats[$code])) {
            $formats[$code] = $formatRows;
        }
    } else {
        $path = BASE_DIR_PATH.'format'.DIRECTORY_SEPARATOR.$code.'.txt';
        if (!is_file($path)) {
            $path = BASE_DIR_PATH.'format'.DIRECTORY_SEPARATOR.'gb.txt';
			if (!is_file($path)) {
				throw new Exception(getStringInLang('response-messages', 33));
			}
        }
        if (!isset($format)) {
            $formatRows = file($path, FILE_IGNORE_NEW_LINES);
            if (APCU_ENABLED) {
                apcu_add($key, $formatRows);
            }
            $formats[$code] = $formatRows;
            if ($row<=0 || $row>count($formatRows)) {
                throw new Exception(getStringInLang('response-messages', 34).getStringInLang('response-messages', 3).getStringInLang('response-messages', 35).getStringInLang('response-messages', 36).$code.getStringInLang('responses-messages', 37).'.');
            } else {
                $format = $formatRows[$index];
            } 
        }
    }
    return $format;
}
