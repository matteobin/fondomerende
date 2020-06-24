<?php
if (API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    $dbManager->lockTables(array('snacks'=>'r'));
    if (setRequestInputValue($snackName, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
        require COMMANDS_PATH.'get-snack-data.php';
        $response = getSnackData($snackName);
    }
}
