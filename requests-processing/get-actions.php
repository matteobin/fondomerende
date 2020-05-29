<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    if (setRequestInputValue($limit, true, 'limit', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
        $offset = 0;
        if (setRequestInputValue($offset, false, 'offset', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>-1))) {
            $order = 'DESC';
            if (setRequestInputValue($ascOrder, false, 'asc-order', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                if ($ascOrder) {
                    $order = 'ASC';
                }
                require BASE_DIR_PATH.'commands/get-actions.php';
                $dbManager->lockTables(array('actions'=>'r', 'users'=>'r', 'edits'=>'r', 'snacks'=>'r'));
                $response = getActions(false, $limit, $offset, $order);
            }
        }
    }
}
