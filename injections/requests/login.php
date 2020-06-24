<?php
if (!API_REQUEST || checkRequestMethod('POST')) {
    if (setRequestInputValue($userName, true, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>30))) {
        if (setRequestInputValue($password, true, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>255))) {
            $rememberUser = false;
            if (setRequestInputValue($rememberUser, false, 'remember-user', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                require COMMANDS_PATH.'login.php';
                $dbManager->lockTables(array('users'=>'w', 'tokens'=>'w'));
                $response = login($userName, $password, $rememberUser);
            }
        }
    }
}
