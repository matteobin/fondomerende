<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('GET', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    if (setRequestInputValue($timestamp, false, 'timestamp', array('filter'=>FILTER_SANITIZE_STRING), array('timestamp'=>true))) {
        if (!isset($timestamp)) {
            $timestamp = (new DateTime())->format('Y-m-d H:i:s');
        }
        require COMMANDS_PATH.'get-actions.php';
        $dbManager->lockTables(array('actions'=>'r', 'users'=>'r', 'edits'=>'r', 'snacks'=>'r'));
        $response = getActions($dbManager, $timestamp, false, false, 'DESC');
    }
}
