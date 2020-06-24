<?php
while (true) {
    if (API_REQUEST && (!checkRequestMethod('POST')||!checkToken())) {
        break;
    }
    $dbManager->lockTables(array('actions'=>'w', 'edits'=>'w', 'users'=>'w'));
    $values = array();
    if (!setRequestInputValue($values, false, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>30, 'database'=>array('table'=>'users', 'select-column'=>'name', 'value-type'=>'s', 'check-type'=>'insert-unique', 'exceptions'=>array($dbManager->getByUniqueId('name', 'users', $_SESSION['user-id'])))))) {
        break;
    } else if (isset($values['name'])) {
        $types['name'] = 's';
    }
    if (!setRequestInputValue($values, false, 'friendly-name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60))) {
        break;
    } else if (isset($values['friendly_name'])) {
        $types['friendly_name'] = 's';
    }
    if (API_REQUEST) {
        if (!setRequestInputValue($values, false, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
            break;
        }
    } else {
        if (!setRequestInputValue($values, false, 'password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125, 'can-be-empty'=>true))) {
            break;
        }
        if ($values['password']=='') {
            unset($values['password']);
        }
        if (!setRequestInputValue($currentPassword, true, 'current-password', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>125))) {
            break;
        }
        require BASE_DIR_PATH.'check-user-password.php';
        if (!checkUserPassword($_SESSION['user-id'], $currentPassword)) {
            $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('edit-user', 6));
            break;
        }
    }
    if (isset($values['password'])) {
        $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
        $types['password'] = 's';
    }
    require COMMANDS_PATH.'edit-snack-or-user.php';
    $response = editSnackOrUser(array('user'=>$_SESSION['user-id']), $values, $types);
    break;
}
