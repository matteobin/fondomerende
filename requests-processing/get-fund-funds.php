<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require BASE_DIR_PATH.'commands/get-fund-funds.php';
    $dbManager->lockTables(array('fund_funds'=>'r'));
    $response = getFundFunds();
}
