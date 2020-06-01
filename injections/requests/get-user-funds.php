<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require BASE_DIR_PATH.'functions/commands/get-user-funds.php';
    $dbManager->lockTables(array('users_funds'=>'r'));
    $response = getUserFunds($_SESSION['user-id']);
}
