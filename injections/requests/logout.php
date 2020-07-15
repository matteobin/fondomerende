<?php
if (!API_REQUEST || checkRequestMethod('POST')&&checkToken()) {
    require COMMANDS_PATH.'logout.php';
    $dbManager->lockTables(array('tokens'=>'w'));
    $response = logout($dbManager);
}
