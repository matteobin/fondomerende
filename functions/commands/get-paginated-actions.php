<?php
require COMMANDS_PATH.'get-actions.php';
function getPaginatedActions($limit, $page, $order) {
    global $dbManager;
    $actions = getActions(false, $limit, ($page-1)*$limit, $order, false);
    $dbManager->query('SELECT count(id) as actions_total FROM actions ORDER BY created_at '.$order);
    $actionsTotal = $dbManager->result->fetch_row()[0];
    $response['success'] = true;
    if (empty($actions)) {
        $response['status'] = 404;
    } else {
        $response['status'] = 200;
        $response['data']['actions'] = $actions;
    }
    $response['data']['actions-total'] = (int)$actionsTotal;
    $response['data']['available-pages'] = (int)ceil($actionsTotal/$limit); 
    return $response;
}
