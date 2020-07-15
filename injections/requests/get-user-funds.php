<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require COMMANDS_PATH.'get-user-funds.php';
    $dbManager->lockTables(array('users_funds'=>'r'));
    $response = getUserFunds($dbManager, $_SESSION['user-id']);
}
