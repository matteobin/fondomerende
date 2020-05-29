<?php
if (!API_REQUEST || checkRequestMethod('POST')&&checkToken()) {
    $dbManager->lockTables(['actions'=>'w', 'edits'=>'w', 'users'=>'w']);
    $values = [];
    if (setRequestInputValue($values, false, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>30, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique', 'exceptions'=>array($dbManager->getByUniqueId('name', 'users', $_SESSION['user-id'])))))) {
        if (isset($values['name'])) {
            $types['name'] = 's';
        }
        if (setRequestInputValue($values, false, 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60))) {
            if (isset($values['friendly_name'])) {
                $types['friendly_name'] = 's';
            }
            $passwordOptions = ['max-length'=>125];
            if (!API_REQUEST) {
                $passwordOptions['can-be-empty'] = true;
            }
            if (setRequestInputValue($values, false, 'password', array('filter'=>FILTER_SANITIZE_STRING), $passwordOptions)) {
                if (isset($values['password']) && $values['password']=='') {
                    unset($values['password']);
                }
                if (setRequestInputValue($currentPassword, !API_REQUEST, 'current-password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
                    if (!API_REQUEST) {
                        require BASE_DIR_PATH.'check-user-password.php';
                        if (!checkUserPassword($_SESSION['user-id'], $currentPassword)) {
                            $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('edit-user', 6));
                            break;
                        }
                    }
            }
            if (isset($values['password'])) {
                $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
                $types['password'] = 's';
            }
            require BASE_DIR_PATH.'commands/edit-snack-or-user.php';
        $response = editSnackOrUser(array('user'=>$_SESSION['user-id']), $values, $types);
}
