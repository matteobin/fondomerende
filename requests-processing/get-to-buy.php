<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require BASE_DIR_PATH.'commands/get-to-buy.php';
    $dbManager->lockTables(array('snacks'=>'r'));
    $response = getToBuy();
}
    