<?php
if (!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('GET', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) {
    if (setRequestInputValue($limit, true, 'limit', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
        $offset = 0;
        if (setRequestInputValue($offset, false, 'offset', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>-1))) {
            $order = 'DESC';
            if (setRequestInputValue($ascOrder, false, 'asc-order', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                if ($ascOrder) {
                    $order = 'ASC';
                }
                require COMMANDS_PATH.'get-actions.php';
                $response = getActions($dbManager, false, $limit, $offset, $order);
            }
        }
    }
}
