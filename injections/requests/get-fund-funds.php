<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('GET', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    require COMMANDS_PATH.'get-fund-funds.php';
    $dbManager->lockTables(array('fund_funds'=>'r'));
    $response = getFundFunds($dbManager);
}
