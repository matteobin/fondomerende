<?php
function checkUserAdmin(DbManager $dbManager, &$response) {
    $dbManager->query('SELECT admin FROM users WHERE id=?', array($_SESSION['user-id']), 'i');
    $isAdmin = false;
    while ($row = $dbManager->result->fetch_row()) {
        $isAdmin = (bool)$row[0];
    }
    if (!$isAdmin) {
        $response = array('success'=>true, 'status'=>401, 'message'=>getStringInLang('response-messages', 7).getStringInLang('response-messages', 32));
    }
    return $isAdmin;
}
if ((!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('POST', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) && (require FUNCTIONS_PATH.'check-user-active.php') && checkUserActive($dbManager, $response) && checkUserAdmin($dbManager, $response)) {
    $verbose = 0;
    if (setRequestInputValue($verbose, false, 'verbose', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>-1, 'less-than'=>3))) {
        require COMMANDS_PATH.'delete-expired-tokens.php';
        $response = deleteExpiredTokens($dbManager, $verbose);
    }
}
