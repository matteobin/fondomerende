<?php
if ((!API_REQUEST || checkRequestMethod('POST')&&checkToken()) && checkUserActive()) {
    $dbManager->lockTables(array('crates'=>'w', 'snacks_stock'=>'w', 'eaten'=>'w', 'users_funds'=>'w', 'actions'=>'w'));
    if (setRequestInputValue($snackId, true, 'id', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>100, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
        $quantity = 1;
        if (setRequestInputValue($quantity, false, 'quantity', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
            require COMMANDS_PATH.'eat.php';
            $response = eat($_SESSION['user-id'], $snackId, $quantity);
        }
    }
}
