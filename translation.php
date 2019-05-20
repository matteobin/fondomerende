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
    $cacheKey = $_SESSION['user-lang'].'_lang_'.$fileName;
    if (apcu_exists($cacheKey)) {
        $cachedTranslatedRows = apcu_fetch($cacheKey);
    }
    if (isset($cachedTranslatedRows[$rowIndex])) {
        $translatedString = $cachedTranslatedRows[$rowIndex];
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
        apcu_add($cacheKey, $translationRows);
        $_SESSION['lang'][$lang][$fileName] = $translationRows;
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
