<?php
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