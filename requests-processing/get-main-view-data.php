<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require BASE_DIR_PATH.'commands/get-main-view-data.php';
    $dbManager->lockTables(array('fund_funds'=>'r', 'users_funds'=>'r', 'actions'=>'r', 'users'=>'r', 'edits'=>'r', 'snacks'=>'r'));
    $response = getMainViewData($_SESSION['user-id']);
}
