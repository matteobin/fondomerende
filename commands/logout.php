<?php
function logout() {
    global $dbManager, $apiRequest;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES tokens WRITE');
        $dbManager->runPreparedQuery('DELETE FROM tokens WHERE token=?', array($_SESSION['token']), 's');
        if (!$apiRequest) {
            unset($_COOKIE['token']);
            unset($_COOKIE['remember-user']);
            $expires = time()-86400;
            require 'set-fm-cookie.php';
            setFmCookie('token', null, $expires);
            setFmCookie('remember-user', null, $expires);
        }
        session_unset();
        session_destroy();
        $response = array('success'=>true, 'status'=>200);
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();   
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
	return $response;
}
