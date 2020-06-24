<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require COMMANDS_PATH.'get-snacks-data.php';
    $dbManager->lockTables(array('snacks'=>'r'));
    $response = getSnacksData();
}
