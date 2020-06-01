<?php
function setFmCookie($name, $value, $expires) {
    $httponly = API_REQUEST ? false : true;
    if (version_compare(phpversion(), '7.3.0', '>=')) {
        $options = array('expires'=>$expires, 'path'=>WEB_BASE_DIR, 'httponly'=>$httponly, 'samesite'=>'Strict');
        setcookie($name, $value, $options);
    } else {
        setcookie($name, $value, $expires, WEB_BASE_DIR, '', false, $httponly);
    }
}
