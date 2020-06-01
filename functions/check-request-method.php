<?php
function checkRequestMethod($acceptedMethod) {
    global $response;
    $requestMethodRight = true;
    if (REQUEST_METHOD!=$acceptedMethod) {
        $requestMethodRight = false;
        $response = array('success'=>false, 'status'=>405, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 4).$acceptedMethod.getTranslatedString('response-messages', 5));
    }
    return $requestMethodRight;
}