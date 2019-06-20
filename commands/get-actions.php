<?php
require('decode-actions.php');
function getActions($limit, $offset, $order, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();
        }
        $dbManager->runPreparedQuery('SELECT * FROM actions ORDER BY created_at '.$order.' LIMIT ? OFFSET ?', array($limit, $offset), 'ii');
        while ($actionsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $actions[] = array('id'=>$actionsRow['id'], 'user-id'=>$actionsRow['user_id'], 'command-id'=>$actionsRow['command_id'], 'snack-id'=>$actionsRow['snack_id'], 'snack-quantity'=>$actionsRow['snack_quantity'], 'funds-amount'=>$actionsRow['funds_amount'], 'created-at'=>$actionsRow['created_at']);
        }
        $decodedActions = array();
        if (isset($actions)) {
            $decodedActions = decodeActions($actions);
        }
        if ($apiCall) {
            $dbManager->endTransaction();
            $response['success'] = true;
            if (empty($decodedActions)) {
                $response['status'] = 404;
            } else {
                $response['status'] = 200;
                $response['data']['actions'] = $decodedActions;
            } 
        }
    } catch (Exception $exception) {
        if ($apiCall) {
            $dbManager->rollbackTransaction();
            $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
        } else {
            throw new Exception($exception->getMessage());
        }
    }
    if ($apiCall) {
       return $response; 
    } else {
        return $decodedActions;
    }
}
