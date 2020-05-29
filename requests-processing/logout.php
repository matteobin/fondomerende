<?php
if (!API_REQUEST || checkRequestMethod('POST')&&checkToken()) {
    require BASE_DIR_PATH.'commands/logout.php';
    $dbManager->lockTables(array('tokens'=>'w'));
    $response = logout();
}
