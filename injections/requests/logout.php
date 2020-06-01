<?php
if (!API_REQUEST || checkRequestMethod('POST')&&checkToken()) {
    require BASE_DIR_PATH.'functions/commands/logout.php';
    $dbManager->lockTables(array('tokens'=>'w'));
    $response = logout();
}
