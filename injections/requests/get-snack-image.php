<?php
if (API_REQUEST && checkRequestMethod('GET') && checkToken()) {
    $dbManager->lockTables(array('snacks'=>'r'));
    if (setRequestInputValue($snackName, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence')))) {
        $overwrite = false;
        if (setRequestInputValue($overwrite, false, 'overwrite', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
            require COMMANDS_PATH.'get-snack-image.php';
            $response = getSnackImage($snackName, $overwrite);
        }
    }
}
