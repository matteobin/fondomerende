<?php
if (!API_REQUEST || checkRequestMethod('GET')&&checkToken()) {
    if (setRequestInputValue($limit, true, 'limit', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
        if (setRequestInputValue($page, true, 'page', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0))) {
            $order = 'DESC';
            if (setRequestInputValue($ascOrder, false, 'asc-order', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
                if ($ascOrder) {
                    $order = 'ASC';
                }
                require COMMANDS_PATH.'get-paginated-actions.php';
                $dbManager->lockTables(array('actions'=>'r', 'users'=>'r', 'edits'=>'r', 'snacks'=>'r'));
                $response = getPaginatedActions($dbManager, $limit, $page, $order);
            }
        }
    }
}
