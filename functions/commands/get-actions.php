<?php
require FUNCTIONS_PATH.'get-by-id.php';
function getUserFriendlyName(DbManager $dbManager, $userId) {
    if ($_SESSION['user-id']==$userId) {
        return $_SESSION['user-friendly-name'];
    } else {
        return getById($dbManager, 'friendly_name', 'users', $userId);
    }
}
function decodeEdits(DbManager $dbManager, $editType, $actionId, $userId, $snackId=null) {
    $userEdit = false;
    if ($editType=='user') {
        $userEdit = true;
    }
    $dbManager->query('SELECT column_name, old_s_value, new_s_value, old_d_value, new_d_value, old_i_value, new_i_value FROM edits WHERE action_id=?', array($actionId), 'i');
    while ($row = $dbManager->result->fetch_assoc()) {
        $edits[$row['column_name']] = array('old-s-value'=>$row['old_s_value'], 'new-s-value'=>$row['new_s_value'], 'old-d-value'=>$row['old_d_value'], 'new-d-value'=>$row['new_d_value'], 'old-i-value'=>$row['old_i_value'], 'new-i-value'=>$row['new_i_value']);
    }
    $decodedEdits = array();
    if (isset($edits)) {
        foreach($edits as $columnName=>$edit) {
            $editSentence = getUserFriendlyName($dbManager, $userId).getTranslatedString('actions', 4);
            switch ($columnName) {
                case 'name':
                    $editSentence .= getTranslatedString('actions', 5);
                    if ($userEdit) {
                        $editSentence .= getTranslatedString('actions', 12).$edit['old-s-value'].getTranslatedString('actions', 13).$edit['new-s-value'].'.';
                    } else {
                        $editSentence .= getTranslatedString('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).$edits['friendly_name']['old-s-value'].getTranslatedString('actions', 13).$edits['friendly_name']['new-s-value'].'.';
                    }
                    $decodedEdits[] = $editSentence;
                    break;
                case 'friendly_name':
                    if ($userEdit) {
                        $editSentence .= getTranslatedString('actions', 6).getTranslatedString('actions', 12).$edit['old-s-value'].getTranslatedString('actions', 13).$edit['new-s-value'].'.';
                        $decodedEdits[] = $editSentence;
                    }
                    break;
                case 'password':
                    $editSentence .= getTranslatedString('actions', 7).'.';
                        $decodedEdits[] = $editSentence;
                    break;
                case 'price':
                    $editSentence .= getTranslatedString('actions', 8).getTranslatedString('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).number_format($edit['old-d-value'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)).' €'.getTranslatedString('actions', 13).number_format($edit['new-d-value'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)).' €.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'snacks_per_box':
                    $editSentence .= getTranslatedString('actions', 9).getTranslatedString('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).$edit['old-i-value'].getTranslatedString('actions', 13).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'expiration_in_days':
                    $editSentence .= getTranslatedString('actions', 10).getTranslatedString('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12).$edit['old-i-value'].getTranslatedString('actions', 13).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'visible':
                    $editSentence .= getById($dbManager, 'friendly_name', 'snacks', $snackId).getTranslatedString('actions', 12);
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
function decodeActions(DbManager $dbManager, $actions) {
    $decodedActions = array();
    foreach($actions as $action) {
        switch ($action['command-id']) {
            case 1:
                $decodedActions[] = $action['created-at'].': '.getUserFriendlyName($dbManager, $action['user-id']).getTranslatedString('actions', 3);
                break;
            case 2:
                $decodedEdits = decodeEdits($dbManager, 'user', $action['id'], $action['user-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 3:
                $decodedActions[] = $action['created-at'].': '.getUserFriendlyName($dbManager, $action['user-id']).getTranslatedString('actions', 15).number_format($action['funds-amount'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)).' €.';
                break;
            case 4:
                $decodedActions[] = $action['created-at'].': '.getUserFriendlyName($dbManager, $action['user-id']).getTranslatedString('actions', 16).number_format($action['funds-amount'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)).' €.';
                break;
            case 5:
                $decodedActions[] = $action['created-at'].': '.getUserFriendlyName($dbManager, $action['user-id']).getTranslatedString('actions', 14).getTranslatedString('snack', 2).' '.getById($dbManager, 'friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 6:
                $decodedEdits = decodeEdits($dbManager, 'snack', $action['id'], $action['user-id'], $action['snack-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 7:
                $decodedActions[] = $action['created-at'].': '.getUserFriendlyName($dbManager, $action['user-id']).getTranslatedString('actions', 17).$action['snack-quantity'].' '.getById($dbManager, 'friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 8:
                $decodedActions[] = $action['created-at'].': '.getUserFriendlyName($dbManager, $action['user-id']).getTranslatedString('actions', 18).$action['snack-quantity'].' '.getById($dbManager, 'friendly_name', 'snacks', $action['snack-id']).'.';
                break;
        }
    }
    return $decodedActions;
}
function getActions(DbManager $dbManager, $timestamp, $limit, $offset, $order, $apiCall=true) {
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
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction(true);
    }
    $dbManager->query($query, $params, $types);
    while ($row = $dbManager->result->fetch_assoc()) {
        $actions[] = array('id'=>$row['id'], 'user-id'=>$row['user_id'], 'command-id'=>$row['command_id'], 'snack-id'=>$row['snack_id'], 'snack-quantity'=>$row['snack_quantity'], 'funds-amount'=>$row['funds_amount'], 'created-at'=>$row['created_at']);
    }
    $decodedActions = array();
    if (isset($actions)) {
        $decodedActions = decodeActions($dbManager, $actions);
    }
    if ($apiCall) {
        $response['success'] = true;
        if (empty($decodedActions)) {
            $response['status'] = 404;
        } else {
            $response['status'] = 200;
            $response['data']['actions'] = $decodedActions;
        }
    }
    if ($apiCall) {
       return $response; 
    } else {
        return $decodedActions;
    }
}
