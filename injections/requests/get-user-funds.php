<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('GET', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    require COMMANDS_PATH.'get-user-funds.php';
    $dbManager->lockTables(array('users_funds'=>'r'));
    $response = getUserFunds($dbManager, $_SESSION['user-id']);
}
