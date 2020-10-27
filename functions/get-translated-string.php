<?php
function getTranslatedString($fileName, $rowNumber) {
    global $translatedStrings;
    if (!isset($translatedStrings)) {
        $translatedStrings = array();
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
    $rowIndex = $rowNumber-1;
    $cacheKey = 'fm-'.$_SESSION['lang'].'-'.$fileName.'-lang';
    if (isset($translatedStrings[$fileName], $translatedStrings[$fileName][$rowIndex])) {
        $translatedString = $translatedStrings[$fileName][$rowIndex];
    } else if (APCU_ENABLED && apcu_exists($cacheKey) && ($translationRows=apcu_fetch($cacheKey)) && isset($translationRows[$rowIndex])) {
        $translatedString = $translationRows[$rowIndex];
        if (!isset($translatedStrings[$fileName])) {
            $translatedStrings[$fileName] = $translationRows;
        }
    } else {
        $filePath = BASE_DIR_PATH.'lang'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$fileName.'.txt';
        if (!is_file($filePath)) {
            if ($lang=='en') {
                $translatedString = 'Invalid translation file name: there is no '.$fileName.' for en lang.';
            } else {
                $filePath = BASE_DIR_PATH.'lang'.DIRECTORY_SEPARATOR.'en'.DIRECTORY_SEPARATOR.$fileName.'.txt';
            }
        }
        if (!isset($translatedString)) {
            $translationRows = file($filePath, FILE_IGNORE_NEW_LINES);
            if (APCU_ENABLED) {
                apcu_add($cacheKey, $translationRows);
            }
            $translatedStrings[$fileName] = $translationRows;
            if ($rowNumber<=0 || $rowNumber>count($translationRows)) {
                $translatedString = 'Invalid translation row number: there is no row number '.$rowNumber.' in '.$lang.' '.$fileName.' lang file.';
            } else {
                $translatedString = $translationRows[$rowIndex];
            } 
        }
    }
    return $translatedString;
}
