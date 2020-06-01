<?php
$format = 'Y-m-d';
if (isset($options['timestamp'])) {
    $valueType = 'timestamp';
    $format .= ' H:i:s';
} else {
    $valueType = 'date';
}
if (!$dateTime = DateTime::createFromFormat($format, $value)) {
    $valid = false;
    $message = '\''.$value.'\''.getTranslatedString('response-messages', 24);
    if (isset($options['timestamp'])) {
        $message .= getTranslatedString('response-messages', 25);
    }
    $message .= getTranslatedString('response-messages', 26);
}
if ($valid && isset($options[$valueType]['greater-than']) && $dateTime<=$options[$valueType]['greater-than']) {
    $valid = false;
    $message = '\''.$value.'\''.getTranslatedString('response-messages', 13).$options[$valueType]['greater-than']->format($format).'.';
}
if ($valid && isset($options[$valueType]['less-than']) && $dateTime>=$options[$valueType]['less-than']) {
    $valid = false;
    $message = '\''.$value.'\''.getTranslatedString('response-messages', 14).$options[$valueType]['less-than']->format($format).'.';
}
