<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require COMMANDS_PATH.'get-to-eat-and-user-funds.php';
    $dbManager->lockTables(array('users_funds'=>'r', 'snacks_stock'=>'r', 'crates'=>'r', 'snacks'=>'r'));
    $response = getToEatAndUserFunds($_SESSION['user-id']);
}