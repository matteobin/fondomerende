<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('GET', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    require COMMANDS_PATH.'get-snacks-data.php';
    $dbManager->lockTables(array('snacks'=>'r'));
    $response = getSnacksData($dbManager);
}