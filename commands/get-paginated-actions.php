<?php
require 'get-actions.php';
function getPaginatedActions($limit, $page, $order) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES actions READ, users READ, edits READ, snacks READ');
        $actions = getActions(false, $limit, ($page-1)*$limit, $order, false);
        $dbManager->runQuery('SELECT count(id) as actions_total FROM actions ORDER BY created_at '.$order);
        while ($actionsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $actionsTotal = $actionsRow['actions_total'];
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        $response['success'] = true;
        if (empty($actions)) {
            $response['status'] = 404;
        } else {
            $response['status'] = 200;
            $response['data']['actions'] = $actions;
        }
        $response['data']['actions-total'] = (int)$actionsTotal;
        $response['data']['available-pages'] = (int)ceil($actionsTotal/$limit);

    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }  
    return $response;
}
