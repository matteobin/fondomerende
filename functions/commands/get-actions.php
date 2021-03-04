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
            $editSentence = getUserFriendlyName($dbManager, $userId).getStringInLang('actions', 4);
            switch ($columnName) {
                case 'name':
                    $editSentence .= getStringInLang('actions', 5);
                    if ($userEdit) {
                        $editSentence .= getStringInLang('actions', 12).$edit['old-s-value'].getStringInLang('actions', 13).$edit['new-s-value'].'.';
                    } else {
                        $editSentence .= getStringInLang('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getStringInLang('actions', 12).$edits['friendly_name']['old-s-value'].getStringInLang('actions', 13).$edits['friendly_name']['new-s-value'].'.';
                    }
                    $decodedEdits[] = $editSentence;
                    break;
                case 'friendly_name':
                    if ($userEdit) {
                        $editSentence .= getStringInLang('actions', 6).getStringInLang('actions', 12).$edit['old-s-value'].getStringInLang('actions', 13).$edit['new-s-value'].'.';
                        $decodedEdits[] = $editSentence;
                    }
                    break;
                case 'password':
                    $editSentence .= getStringInLang('actions', 7).'.';
                        $decodedEdits[] = $editSentence;
                    break;
                case 'price':
                    $editSentence .= getStringInLang('actions', 8).getStringInLang('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getStringInLang('actions', 12).number_format($edit['old-d-value'], 2, getFormat(1), getFormat(2)).' €'.getStringInLang('actions', 13).number_format($edit['new-d-value'], 2, getFormat(1), getFormat(2)).' €.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'snacks_per_box':
                    $editSentence .= getStringInLang('actions', 9).getStringInLang('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getStringInLang('actions', 12).$edit['old-i-value'].getStringInLang('actions', 13).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'expiration_in_days':
                    $editSentence .= getStringInLang('actions', 10).getStringInLang('actions', 11).getById($dbManager, 'friendly_name', 'snacks', $snackId).getStringInLang('actions', 12).$edit['old-i-value'].getStringInLang('actions', 13).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'visible':
                    $editSentence .= getById($dbManager, 'friendly_name', 'snacks', $snackId).getStringInLang('actions', 12);
                    if ($edit['old-i-value']==1) {
                        $editSentence .= getStringInLang('snack', 8);
                    } else {
                        $editSentence .= getStringInLang('snack', 7);
                    }
                    $editSentence .= getStringInLang('actions', 13);
                    if ($edit['new-i-value']==1) {
                        $editSentence .= getStringInLang('snack', 8);
                    } else {
                        $editSentence .= getStringInLang('snack', 7);
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
		$createdAt = new DateTime($action['created-at']);
		$createdAt = $createdAt->format(getFormat(3).' '.getFormat(4));
        switch ($action['command-id']) {
            case 1:
                $decodedActions[] = $createdAt.': '.getUserFriendlyName($dbManager, $action['user-id']).getStringInLang('actions', 3);
                break;
            case 2:
                $decodedEdits = decodeEdits($dbManager, 'user', $action['id'], $action['user-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $createdAt.': '.$decodedEdit;
                }
                break;
            case 3:
                $decodedActions[] = $createdAt.': '.getUserFriendlyName($dbManager, $action['user-id']).getStringInLang('actions', 15).number_format($action['funds-amount'], 2, getFormat(1), getFormat(2)).' €.';
                break;
            case 4:
                $decodedActions[] = $createdAt.': '.getUserFriendlyName($dbManager, $action['user-id']).getStringInLang('actions', 16).number_format($action['funds-amount'], 2, getFormat(1), getFormat(2)).' €.';
                break;
            case 5:
                $decodedActions[] = $createdAt.': '.getUserFriendlyName($dbManager, $action['user-id']).getStringInLang('actions', 14).getStringInLang('snack', 2).' '.getById($dbManager, 'friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 6:
                $decodedEdits = decodeEdits($dbManager, 'snack', $action['id'], $action['user-id'], $action['snack-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $createdAt.': '.$decodedEdit;
                }
                break;
            case 7:
                $decodedActions[] = $createdAt.': '.getUserFriendlyName($dbManager, $action['user-id']).getStringInLang('actions', 17).$action['snack-quantity'].' '.getById($dbManager, 'friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 8:
                $decodedActions[] = $createdAt.': '.getUserFriendlyName($dbManager, $action['user-id']).getStringInLang('actions', 18).$action['snack-quantity'].' '.getById($dbManager, 'friendly_name', 'snacks', $action['snack-id']).'.';
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
