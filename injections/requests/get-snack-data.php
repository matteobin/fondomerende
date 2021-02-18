<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('GET', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    if (setRequestInputValue($snackName, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'existence', 'read-transaction'=>true)))) {
        require COMMANDS_PATH.'get-snack-data.php';
        $response = getSnackData($dbManager, $snackName);
    }
}
