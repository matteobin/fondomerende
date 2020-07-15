<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require COMMANDS_PATH.'get-fund-funds.php';
    $dbManager->lockTables(array('fund_funds'=>'r'));
    $response = getFundFunds($dbManager);
}
