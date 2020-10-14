<?php
if ((!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('POST', $response)&&checkToken($response, $dbManager)) && (require FUNCTIONS_PATH.'check-user-active.php') && checkUserActive($dbManager, $response)) {
    $dbManager->lockTables(array('snacks'=>'r', 'crates'=>'w', 'snacks_stock'=>'w', 'eaten'=>'w', 'users_funds'=>'w', 'actions'=>'w'));
    if (setRequestInputValue($snackId, true, 'id', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>100, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
        $quantity = 1;
        if (setRequestInputValue($quantity, false, 'quantity', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
            require COMMANDS_PATH.'eat.php';
            $response = eat($dbManager, $_SESSION['user-id'], $snackId, $quantity);
        }
    }
}
