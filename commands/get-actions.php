<?php
function decodeEdits($editType, $actionId, $userId, $snackId=null) {
    global $dbManager;
    $userEdit = false;
    if ($editType=='user') {
        $userEdit = true;
    }
    $dbManager->runPreparedQuery('SELECT column_name, old_s_value, new_s_value, old_d_value, new_d_value, old_i_value, new_i_value FROM edits WHERE action_id=?', array($actionId), 'i');
    while ($editsRow = $dbManager->getQueryRes()->fetch_assoc()) {
        $edits[$editsRow['column_name']] = array('old-s-value'=>$editsRow['old_s_value'], 'new-s-value'=>$editsRow['new_s_value'], 'old-d-value'=>$editsRow['old_d_value'], 'new-d-value'=>$editsRow['new_d_value'], 'old-i-value'=>$editsRow['old_i_value'], 'new-i-value'=>$editsRow['new_i_value']);
    }
    $decodedEdits = array();
    if (isset($edits)) {
        foreach($edits as $columnName=>$edit) {
            $editSentence = '';
            switch ($columnName) {
                case 'name':
                    if ($userEdit) {
                        $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId);
                        $editSentence .= getTranslatedString('actions', 4).getTranslatedString('actions', 5).getTranslatedString('actions', 12).$edit['old-s-value'].getTranslatedString('actions', 13).$edit['new-s-value'].'.';
                    } else {
                        $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 4).getTranslatedString('actions', 5).getTranslatedString('actions', 11).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).$edits['friendly_name']['old-s-value'].getTranslatedString('actions', 13).$edits['friendly_name']['new-s-value'].'.';
                    }
                    $decodedEdits[] = $editSentence;
                    break;
                case 'friendly_name':
                    if ($userEdit) {
                        $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 4).getTranslatedString('actions', 6).getTranslatedString('actions', 12).$edit['old-s-value'].getTranslatedString('actions', 13).$edit['new-s-value'].'.';
                        $decodedEdits[] = $editSentence;
                    }
                    break;
                case 'password':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 4).getTranslatedString('actions', 7).'.';
                        $decodedEdits[] = $editSentence;
                    break;
                case 'price':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 4).getTranslatedString('actions', 8).getTranslatedString('actions', 11).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).$edit['old-d-value'].' €'.getTranslatedString('actions', 13).$edit['new-d-value'].' €.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'snacks_per_box':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 4).getTranslatedString('actions', 9).getTranslatedString('actions', 11).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).$edit['old-i-value'].getTranslatedString('actions', 13).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'expiration_in_days':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 4).getTranslatedString('actions', 10).getTranslatedString('actions', 11).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).$edit['old-i-value'].getTranslatedString('actions', 13).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'visible':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 4).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12);
                    if ($edit['old-i-value']==1) {
                        $editSentence .= getTranslatedString('snack', 8);
                    } else {
                        $editSentence .= getTranslatedString('snack', 7);
                    }
                    $editSentence .= getTranslatedString('actions', 13);
                    if ($edit['new-i-value']==1) {
                        $editSentence .= getTranslatedString('snack', 8);
                    } else {
                        $editSentence .= getTranslatedString('snack', 7);
                    }
                    $decodedEdits[] = $editSentence.'.';
                    break;
            }
        }
    }
    return $decodedEdits;
}
function decodeActions($actions) {
    global $dbManager;
    $decodedActions = array();
    foreach($actions as $action) {
        switch ($action['command-id']) {
            case 1:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 3);
                break;
            case 2:
                $decodedEdits = decodeEdits('user', $action['id'], $action['user-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 3:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 15).$action['funds-amount'].' €.';
                break;
            case 4:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 16).$action['funds-amount'].' €.';
                break;
            case 5:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 14).getTranslatedString('snack', 2).' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 6:
                $decodedEdits = decodeEdits('snack', $action['id'], $action['user-id'], $action['snack-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 7:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 17).$action['snack-quantity'].' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 8:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 18).$action['snack-quantity'].' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
        }
    }
    return $decodedActions;
}
function getActions($timestamp, $limit, $offset, $order, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();
            $dbManager->runQuery('LOCK TABLES actions READ, users READ, edits READ, snacks READ');
        }
        $query = 'SELECT * FROM actions ';
        $params = array();
        $types = '';
        if ($timestamp) {
            $query .= 'WHERE actions.created_at>? '; 
            $params[] = $timestamp;
            $types .= 's';
        }
        $query .= 'ORDER BY actions.created_at '.$order; 
        if ($limit) {
            $query .= ' LIMIT ? ';
            $params[] = $limit;
            $types .= 'i';
        }
        if ($offset) {
            $query .= 'OFFSET ?';
            $params[] = $offset;
            $types .= 'i';
        }
        $dbManager->runPreparedQuery($query, $params, $types);
        while ($actionsRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $actions[] = array('id'=>$actionsRow['id'], 'user-id'=>$actionsRow['user_id'], 'command-id'=>$actionsRow['command_id'], 'snack-id'=>$actionsRow['snack_id'], 'snack-quantity'=>$actionsRow['snack_quantity'], 'funds-amount'=>$actionsRow['funds_amount'], 'created-at'=>$actionsRow['created_at']);
        }
        $decodedActions = array();
        if (isset($actions)) {
            $decodedActions = decodeActions($actions);
        }
        if ($apiCall) {
            $dbManager->runQuery('UNLOCK TABLES');
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
