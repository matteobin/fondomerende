<?php
if (API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require BASE_DIR_PATH.'commands/get-snacks-data.php';
    $dbManager->lockTables(array('snacks'=>'r'));
    $response = getSnacksData();
}
