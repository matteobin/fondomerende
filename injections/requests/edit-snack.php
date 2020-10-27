<?php
if ((!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('POST', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) && (require FUNCTIONS_PATH.'check-user-active.php') && checkUserActive($dbManager, $response)) {
    if (setRequestInputValue($snackId, true, 'id', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
        $values = array();
        if (setRequestInputValue($values, false, 'name', array('filter'=>FILTER_SANITIZE_STRING), array('max-length'=>60, 'database'=>array('table'=>'snacks', 'select-column'=>'friendly_name', 'value-type'=>'s', 'check-type'=>'insert-unique', 'exceptions'=>array($dbManager->getByUniqueId('friendly_name', 'snacks', $snackId)))))) {
            if (isset($values['name'])) {
                $types['name'] = 's';
                $values['friendly_name'] = $values['name'];
                $types['friendly_name'] = 's';
                $values['name'] = str_replace(' ', '-', strtolower($values['name']));
            }
            if (setRequestInputValue($values, false, 'price', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2, 'less-than'=>100))) {
                if (isset($values['price'])) {
                    $types['price'] = 'd';
                }
                if (setRequestInputValue($values, false, 'snacks-per-box', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>1000))) {
                    if (isset($values['snacks_per_box'])) {
                        $types['snacks_per_box'] = 'i';
                    }
                    if (setRequestInputValue($values, false, 'expiration-in-days', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>10000))) {
                        if (isset($values['expiration_in_days'])) {
                            $types['expiration_in_days'] = 'i';
                        }
                        if (setRequestInputValue($values, false, 'visible', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                            if (isset($values['visible'])) {
                                $types['visible'] = 'i';
                            }
                            require COMMANDS_PATH.'edit-snack-or-user.php';
                            $response = editSnackOrUser($dbManager, array('user'=>$_SESSION['user-id'], 'snack'=>$snackId), $values, $types);
                        }
                    }
                }
            }
        }
    }
}
