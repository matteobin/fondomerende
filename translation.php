<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function getTranslatedString($fileName, $rowNumber) {
    if (isset($_GET['lang'])) {
        $lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
    } else if (isset($_SESSION['user-lang'])) {
        $lang = $_SESSION['user-lang'];
    } else {
        $lang = substr(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0, 2);
        $_SESSION['user-lang'] = $lang;
    }
    $rowIndex = $rowNumber-1;
    $rowsCacheKey = $_SESSION['user-lang'].$fileName.'translation-rows';
    $rowsGlobalVariableName = $_SESSION['user-lang'].str_replace('-', '', ucwords($fileName, '-')).'TranslationRows';
    global $$rowsGlobalVariableName;
    if (isset($$rowsGlobalVariableName[$rowIndex])) {
        $translatedString = $$rowsGlobalVariableName[$rowIndex];
    }
    else if (APCU_CACHE_INSTALLED && apcu_exists($rowsCacheKey) && isset(($cachedTranslatedRows = apcu_fetch($rowsCacheKey))[$rowIndex])) {
        $translatedString = $cachedTranslatedRows[$rowIndex];
        if (isset($$rowsGlobalVariableName)) {
            $$rowsGlobalVariableName[$rowIndex] = $translatedString;
        } else {
            $$rowsGlobalVariableName = $cachedTranslatedRows;
        }
    } else {
        $filePath = '../lang/'.$lang.'/'.$fileName.'.txt';
        if (!is_file($filePath)) {
            if ($lang=='en') {
                $translatedString = 'Invalid translation file name: there is no '.$fileName.' for en lang.';
            } else {
                $filePath = '../lang/en/'.$fileName.'.txt';
            }
        }
        if (!isset($translatedString)) {
            $translationRows = file($filePath, FILE_IGNORE_NEW_LINES);
            if (APCU_CACHE_INSTALLED) {
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
    echo(getTranslatedString($fileName, $rowNumber));
}

function echoUcfirstTranslatedString($fileName, $rowNumber) {
    echo(ucfirst(getTranslatedString($fileName, $rowNumber)));
}

function echoStrtoupperTranslatedString($fileName, $rowNumber) {
    echo(strtoupper(getTranslatedString($fileName, $rowNumber)));
}
