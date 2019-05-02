<?php
function getTranslationRows($lang, $fileName) {
    $translationRows = file('../lang/'.$lang.'/'.$fileName.'.txt', FILE_IGNORE_NEW_LINES);
    if(!$translationRows) {
        $translationRows = file('../lang/en/'.$fileName.'.txt', FILE_IGNORE_NEW_LINES);
    }
    return $translationRows;
}

function getTranslatedString($fileName, $rowNumber) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_GET['lang'])) {
        $lang = filter_input(INPUT_GET, 'lang', FILTER_SANITIZE_STRING);
    } else if (isset($_SESSION['user-lang'])) {
        $lang = $_SESSION['user-lang'];
    } else {
        $lang = substr(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0, 2);
        $_SESSION['user-lang'] = $lang;
    }
    $rowIndex = $rowNumber-1;
    if (isset($_SESSION['lang'][$lang][$fileName])) {
        $translatedString = $_SESSION['lang'][$lang][$fileName][$rowIndex];
    } else {
        $translationRows = getTranslationRows($lang, $fileName);
        $_SESSION['lang'][$lang][$fileName] = $translationRows;
        if (!$translationRows) {
            $translatedString = 'Invalid translation file name: there is no '.$fileName.' for en lang.';
        } else {
            if ($rowNumber<=0 || $rowNumber>count($translationRows)) {
                $translatedString = 'Invalid translation row number: there is no row number '.$rowNumber.' in '.$lang.' '.$fileName.' lang file.';
            } else {
                $translatedString = $translationRows[$rowIndex];
            } 
        }
    }
    return $translatedString;
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
