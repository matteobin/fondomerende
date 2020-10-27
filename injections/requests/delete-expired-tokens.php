<?php
if ((!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('POST', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) && (require FUNCTIONS_PATH.'check-user-active.php') && checkUserActive($dbManager, $response) && (require FUNCTIONS_PATH.'check-user-admin.php') && checkUserAdmin($dbManager, $response)) {
    $verbose = 0;
    if (setRequestInputValue($verbose, false, 'verbose', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>-1, 'less-than'=>3))) {
        require COMMANDS_PATH.'delete-expired-tokens.php';
        $response = deleteExpiredTokens($dbManager, $verbose);
    }
}
