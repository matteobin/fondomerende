<?php
function logout($appRequest) {
    if (!$appRequest) {
        unset($_COOKIE['user-id']);
        unset($_COOKIE['user-friendly-name']);
        unset($_COOKIE['user-token']);
        unset($_COOKIE['remember-user']);
        $expires = time()-86400;
        setFmCookie('user-id', null, $expires);
        setFmCookie('user-friendly-name', null, $expires);
        setFmCookie('user-token', null, $expires);
        setFmCookie('remember-user', null, $expires);
    }
    session_unset();
    session_destroy();
	$response = array('success'=>true, 'status'=>200);
	return $response;
}
