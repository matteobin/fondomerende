<?php
function checkToken() {
    global $apiRequest, $dbManager;
    if (!isset($apiRequest)) {
        $apiRequest = false;
    }
    if (!isset($dbManager)) {
        require '../DbManager.php';
        $dbManager = new DbManager(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
    }
    $isValid = false;
    $token = filter_input(INPUT_COOKIE, 'token', FILTER_SANITIZE_STRING);
    if ($token) {
        if (isset($_SESSION['user-id'], $_SESSION['user-friendly-name'], $_SESSION['token'])) {
            if ($token==$_SESSION['token']) {
                $isValid = true;
            }
        } else {
            // to do: handle possible query exceptions
            $dbManager->startTransaction();
            $nowToday = (new DateTime())->format('Y-m-d H:i:s');
            $dbManager->runQuery('LOCK TABLES tokens WRITE, users READ');
            $dbManager->runPreparedQuery('SELECT users.id, users.friendly_name FROM tokens JOIN users ON tokens.user_id=users.id WHERE tokens.token=? AND (tokens.expires_at<=? OR tokens.expires_at IS NULL)', array($token, $nowToday), 'ss');
            while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
                $isValid = true;
                $_SESSION['user-id'] = $usersRow['id'];
                $_SESSION['user-friendly-name'] = $usersRow['friendly_name'];
                $_SESSION['token'] = $token;
            }
            $device = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
            $dbManager->runPreparedQuery('UPDATE tokens SET device=?, last_used_at=?, api_request=? WHERE token=?', array($device, $nowToday, $apiRequest, $tokenCookie), 'ssis');
            $dbManager->runQuery('UNLOCK TABLES');
            $dbManager->endTransaction();
        }
    } else if ($apiRequest) {
        global $response;
        $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 6));
    }
    return $isValid;
}