<?php
function getStringInLang($topic, $row) {
    global $stringInLangs;
    if (!isset($stringInLangs)) {
        $stringInLangs = array();
    }
    $lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
    if ($lang) {
        $_SESSION['lang'] = $lang;
    } else if (isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
    } else {
        $lang = filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING);
        $lang = $lang ? substr($lang, 0, 2) : 'en';
        $_SESSION['lang'] = $lang;
    }
    $index = $row-1;
    $key = 'fm-'.$_SESSION['lang'].'-'.$topic.'-lang';
    if (isset($stringInLangs[$topic], $stringInLangs[$topic][$index])) {
        $string = $stringInLangs[$topic][$index];
    } else if (APCU_ENABLED && apcu_exists($key) && ($rows=apcu_fetch($key)) && isset($rows[$index])) {
        $string = $rows[$index];
        if (!isset($stringInLangs[$topic])) {
            $stringInLangs[$topic] = $rows;
        }
    } else {
        $path = BASE_DIR_PATH.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$topic.'.txt';
        if (!is_file($path)) {
            if ($lang=='en') {
                throw new Exception('Invalid translation file name: there is no '.$topic.' for en lang.');
            } else {
                $path = BASE_DIR_PATH.'lang'.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.$topic.'.txt';
            }
        }
        if (!isset($string)) {
            $rows = file($path, FILE_IGNORE_NEW_LINES);
            if (APCU_ENABLED) {
                apcu_add($key, $rows);
            }
            $stringInLangs[$topic] = $rows;
            if ($row<=0 || $row>count($rows)) {
                throw new Exception('Invalid translation row number: there is no row number '.$row.' in '.$lang.' '.$topic.' lang file.');
            } else {
                $string = $rows[$index];
            } 
        }
    }
    return $string;
}
