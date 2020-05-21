<?php
session_start();
function getTranslatedString($fileName, $rowNumber) {
    $lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
    if ($lang) {
        $_SESSION['lang'] = $lang;
    } else if (isset($_SESSION['lang'])) {
        $lang = $_SESSION['lang'];
    } else {
        $lang = substr(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0, 2);
        $_SESSION['lang'] = $lang;
    }
    $rowIndex = $rowNumber-1;
    $rowsCacheKey = 'fm-'.$_SESSION['lang'].'-'.$fileName.'-translation-rows';
    $rowsGlobalVariableName = $_SESSION['lang'].str_replace('-', '', ucwords($fileName, '-')).'TranslationRows';
    global $$rowsGlobalVariableName;
    if (isset($$rowsGlobalVariableName[$rowIndex])) {
        $translatedString = $$rowsGlobalVariableName[$rowIndex];
    }
    else if (APCU_INSTALLED && apcu_exists($rowsCacheKey) && isset(($cachedTranslatedRows = apcu_fetch($rowsCacheKey))[$rowIndex])) {
        $translatedString = $cachedTranslatedRows[$rowIndex];
        if (isset($$rowsGlobalVariableName)) {
            $$rowsGlobalVariableName[$rowIndex] = $translatedString;
        } else {
            $$rowsGlobalVariableName = $cachedTranslatedRows;
        }
    } else {
        $filePath = BASE_DIR_PATH.'lang/'.$lang.'/'.$fileName.'.txt';
        if (!is_file($filePath)) {
            if ($lang=='en') {
                $translatedString = 'Invalid translation file name: there is no '.$fileName.' for en lang.';
            } else {
                $filePath = BASE_DIR_PATH.'lang/en/'.$fileName.'.txt';
            }
        }
        if (!isset($translatedString)) {
            $translationRows = file($filePath, FILE_IGNORE_NEW_LINES);
            if (APCU_INSTALLED) {
                apcu_add($rowsCacheKey, $translationRows);
            }
            $$rowsGlobalVariableName = $translationRows;
            if ($rowNumber<=0 || $rowNumber>count($translationRows)) {
                $translatedString = 'Invalid translation row number: there is no row number '.$rowNumber.' in '.$lang.' '.$fileName.' lang file.';
            } else {
                $translatedString = $translationRows[$rowIndex];
            } 
        }
    }
    return $translatedString;
}
function getUcfirstTranslatedString($fileName, $rowNumber) {
    return ucfirst(getTranslatedString($fileName, $rowNumber));
}
function echoTranslatedString($fileName, $rowNumber) {
    echo getTranslatedString($fileName, $rowNumber);
}
function echoUcfirstTranslatedString($fileName, $rowNumber) {
    echo ucfirst(getTranslatedString($fileName, $rowNumber));
}
function echoStrtoupperTranslatedString($fileName, $rowNumber) {
    echo strtoupper(getTranslatedString($fileName, $rowNumber));
}
