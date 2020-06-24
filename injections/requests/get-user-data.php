<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require COMMANDS_PATH.'/get-user-data.php';
    $dbManager->lockTables(array('users'=>'r'));
    $response = getUserData($_SESSION['user-id']);
}
