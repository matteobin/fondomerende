<?php
ini_set('display_errors', true);
function getTranslationRows($lang, $fileName) {
    $translationRows = file('../lang/'.$lang.'/'.$fileName.'.txt');
    if(!$translationRows) {
        $translationRows = file('../lang/en/'.$fileName.'.txt');
    }
    return $translationRows;
}
function getTranslatedString($fileName, $rowNumber) {
    $rowIndex = $rowNumber-1;
    $lang = substr(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0, 2);
    $translationRows = getTranslationRows($lang, $fileName);
    if (!$translationRows) {
        $translatedString = 'Invalid translation file name: there is no '.$fileName.' for en lang.';
    } else {
        if ($rowNumber<=0 || $rowNumber+1>count($translationRows)) {
            $translatedString = 'Invalid translation row number: there is no row number '.$rowNumber.' in '.$lang.' '.$fileName.' lang file.';
        } else {
            $translatedString = $translationRows[$rowIndex];
        } 
    }
    return $translatedString;
}
echo(getTranslatedString('main', 0));
