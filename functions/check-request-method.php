<?php
function checkRequestMethod($accepted, &$response) {
    $isRight = true;
    if (REQUEST_METHOD!=$accepted) {
        $isRight = false;
        $response = array('success'=>false, 'status'=>405, 'message'=>getStringInLang('response-messages', 2).getStringInLang('response-messages', 3).getStringInLang('response-messages', 4).$accepted.getStringInLang('response-messages', 5));
    }
    return $isRight;
}