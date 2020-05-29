<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    if (setRequestInputValue($timestamp, false, 'timestamp', array('filter'=>FILTER_SANITIZE_STRING), array('timestamp'=>true))) {
        if (!isset($timestamp)) {
            $timestamp = (new DateTime())->format('Y-m-d H:i:s');
        }
        require BASE_DIR_PATH.'commands/get-actions.php';
        $dbManager->lockTables(array('actions'=>'r', 'users'=>'r', 'edits'=>'r', 'snacks'=>'r'));
        $response = getActions($timestamp, false, false, 'DESC');
    }
}
