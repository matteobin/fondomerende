<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('GET', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    require COMMANDS_PATH.'get-main-view-data.php';
    $dbManager->lockTables(array('fund_funds'=>'r', 'users_funds'=>'r', 'actions'=>'r', 'users'=>'r', 'edits'=>'r', 'snacks'=>'r'));
    $response = getMainViewData($dbManager, $_SESSION['user-id']);
}
