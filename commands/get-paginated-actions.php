<?php
require('get-actions.php');
function getPaginatedActions($limit, $page, $order) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $actions = getActions(false, $limit, ($page-1)*$limit, $order, false);
        $dbManager->runQuery('SELECT count(id) as all_actions_number FROM actions ORDER BY created_at '.$order);
        while ($actionsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $actionsTotal = $actionsRow['actions_total'];
        }
        $dbManager->endTransaction();
        $availablePages = ceil($actionsTotal/$limit);
        $response['success'] = true;
        $response['data']['actions-total'] = $actionsTotal;
        if (empty($actions)) {
            $response['status'] = 404;
        } else {
            $response['status'] = 200;
            $response['data']['actions'] = $actions;
        }

    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }  
    return $response;
}
