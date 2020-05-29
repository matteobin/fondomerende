<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require BASE_DIR_PATH.'commands/get-user-data.php';
    $dbManager->lockTables(array('users'=>'r'));
    $response = getUserData($_SESSION['user-id']);
}
