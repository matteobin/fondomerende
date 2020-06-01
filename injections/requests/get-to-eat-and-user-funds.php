<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require BASE_DIR_PATH.'functions/commands/get-to-eat-and-user-funds.php';
    $dbManager->lockTables(array('users_funds'=>'r', 'snacks_stock'=>'r', 'crates'=>'r', 'snacks'=>'r'));
    $response = getToEatAndUserFunds($_SESSION['user-id']);
}