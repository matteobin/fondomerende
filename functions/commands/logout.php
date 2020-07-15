<?php
function logout(DbManager $dbManager) {
    $dbManager->query('DELETE FROM tokens WHERE token=?', array($_SESSION['token']), 's');
    if (!API_REQUEST) {
        unset($_COOKIE['token']);
        unset($_COOKIE['remember-user']);
        $expires = time()-86400;
        require FUNCTIONS_PATH.'set-fm-cookie.php';
        setFmCookie('token', null, $expires);
        setFmCookie('remember-user', null, $expires);
    }
    session_unset();
    session_destroy();
    return array('success'=>true, 'status'=>200);
}
