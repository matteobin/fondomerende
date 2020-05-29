<?php
if (!API_REQUEST || checkRequestMethod('POST')) {
    $dbManager->lockTables(array('users'=>'w', 'snacks'=>'r', 'eaten'=>'w', 'users_funds'=>'w', 'actions'=>'w', 'tokens'=>'w'));
    if (setRequestInputValue($name, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>30, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique')))) {
        if (setRequestInputValue($password, true, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
            if (setRequestInputValue($friendlyName, true, 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60))) {
                $admin = false;
                if (setRequestInputValue($admin, false, 'admin', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                    require BASE_DIR_PATH.'commands/add-user.php';
                    $response = addUser($name, $password, $friendlyName, $admin);
                }
            }
        }
    }
}
