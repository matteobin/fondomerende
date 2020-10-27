<?php
function checkToken(&$response, &$dbManager) {
    $isValid = false;
    $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('response-messages', 2).getTranslatedString('response-messages', 3).getTranslatedString('response-messages', 6));
    $token = API_REQUEST ? filter_input(INPUT_SERVER, 'Token', FILTER_SANITIZE_STRING) : filter_input(INPUT_COOKIE, 'token', FILTER_SANITIZE_STRING);
    if ($token) {
        if (isset($_SESSION['user-id'], $_SESSION['user-friendly-name'], $_SESSION['token'])) {
            if ($token==$_SESSION['token']) {
                $isValid = true;
            }
        } else {
            if (!isset($dbManager)) {
                require BASE_DIR_PATH.'DbManager.php';
                $dbManager = new DbManager();
            }
            $dbManager->beginTransaction(MYSQLI_TRANS_START_READ_WRITE);
            $nowToday = new DateTime();
            $nowToday = $nowToday->format('Y-m-d H:i:s'); 
            $dbManager->query('SELECT users.id, users.friendly_name FROM tokens JOIN users ON tokens.user_id=users.id WHERE tokens.token=? AND (tokens.expires_at>? OR tokens.expires_at IS NULL)', array($token, $nowToday), 'ss');
            while ($row = $dbManager->result->fetch_assoc()) {
                $isValid = true;
                $_SESSION['user-id'] = $row['id'];
                $_SESSION['user-friendly-name'] = $row['friendly_name'];
                $_SESSION['token'] = $token;
            }
            $device = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
            $dbManager->query('UPDATE tokens SET device=?, last_used_at=?, api_request=? WHERE token=?', array($device, $nowToday, API_REQUEST, $tokenCookie), 'ssis');
        }
    }
    return $isValid;
}
