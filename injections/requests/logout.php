<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('POST', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    require COMMANDS_PATH.'logout.php';
    $dbManager->lockTables(array('tokens'=>'w'));
    $response = logout($dbManager);
}
