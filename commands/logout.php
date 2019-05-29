<?php
function logout($appRequest) {
    if (!$appRequest) {
        unset($_COOKIE['user-id']);
        unset($_COOKIE['user-token']);
        unset($_COOKIE['user-friendly-name']);
        unset($_COOKIE['remember-user']);
        setcookie('user-id', null, time()-3600);
        setcookie('user-token', null, time()-3600);
        setcookie('user-friendly-name', null, time()-3600);
        setcookie('remember-user', null, time()-3600);
    }
    session_unset();
    session_destroy();
	$response = array('success'=>true, 'status'=>200);
	return $response;
}
