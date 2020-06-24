<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    require COMMANDS_PATH.'get-to-buy.php';
    $dbManager->lockTables(array('snacks'=>'r'));
    $response = getToBuy();
}
    