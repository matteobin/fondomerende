<?php
function checkRequestMethod($accepted, &$response) {
    $isRight = true;
    if (REQUEST_METHOD!=$accepted) {
        $isRight = false;
        $response = array('success'=>false, 'status'=>405, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 4).$accepted.getTranslatedString('response-messages', 5));
    }
    return $isRight;
}