<?php
require('decode-actions.php');
function getLatestActions($sinceTimestamp) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT * FROM actions WHERE created_at>? ORDER BY created_at DESC', array($sinceTimestamp), 's');
        while ($actionsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $actions[] = array('id'=>$actionsRow['id'], 'user-id'=>$actionsRow['user_id'], 'command-id'=>$actionsRow['command_id'], 'snack-id'=>$actionsRow['snack_id'], 'snack-quantity'=>$actionsRow['snack_quantity'], 'funds-amount'=>$actionsRow['funds_amount'], 'created-at'=>$actionsRow['created_at']);
        }
        $decodedActions = array();
        if (isset($actions)) {
            $decodedActions = decodeActions($actions);
        }
        $dbManager->endTransaction();
        $response['success'] = true;
        if (empty($decodedActions)) {
            $response['status'] = 404;
        } else {
            $response['status'] = 200;
            $response['data']['actions'] = $decodedActions;
        }
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
}
