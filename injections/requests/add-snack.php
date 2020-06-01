<?php
if ((!API_REQUEST || checkRequestMethod('POST')&&checkToken()) && checkUserActive()) {
    $dbManager->lockTables(array('snacks'=>'w', 'snacks_stock'=>'w', 'users'=>'r', 'eaten'=>'w', 'actions'=>'w'));
    if (setRequestInputValue($name, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
        if (setRequestInputValue($price, true, 'price', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2, 'less-than'=>100))) {
            if (setRequestInputValue($snacksPerBox, true, 'snacks-per-box', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>1000))) {
                if (setRequestInputValue($expirationInDays, true, 'expiration-in-days', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>10000))) {
                    $countable = true;
                    if (setRequestInputValue($countable, false, 'countable', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                        require BASE_DIR_PATH.'functions/commands/add-snack.php';
                        $response = addSnack($_SESSION['user-id'], $name, $price, $snacksPerBox, $expirationInDays, $countable);
                    }
                }
            }
        }
    }
}
