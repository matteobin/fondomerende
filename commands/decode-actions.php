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
                        if (isset($edits['friendly_name'])) {
                            $editSentence .= $edits['friendly_name']['old-s-value'].' ';
                        } else {
                            $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId);
                        }
                        $editSentence .= getTranslatedString('actions', 1).getTranslatedString('commons', 3).getTranslatedString('actions', 8).$edit['old-s-value'].getTranslatedString('actions', 9).$edit['new-s-value'].'.';
                    } else {
                        $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 1).getTranslatedString('actions', 2).getTranslatedString('actions, 7').$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 8).$edits['friendly_name']['old-s-value'].getTranslatedString('actions', 9).$edits['friendly_name']['new-s-value'].'.';
                    }
                    $decodedEdits[] = $editSentence;
                    break;
                case 'friendly_name':
                    if ($userEdit) {
                        $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 1).' '.getTranslatedString('actions', 3).getTranslatedString('actions', 8).$edit['old-s-value'].getTranslatedString('actions', 9).$edit['new-s-value'].'.';
                        $decodedEdits[] = $editSentence;
                    }
                    break;
                case 'price':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 1).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 4).getTranslatedString('actions', 8).$edit['old-d-value'].' €'.getTranslatedString('actions', 9).$edit['new-d-value'].' €.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'snacks_per_box':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 1).getTranslatedString('actions', 5).getTranslatedString('actions', 7).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 8).$edit['old-i-value'].getTranslatedString('actions', 9).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
                    break;
                case 'expiration_in_days':
                    $editSentence .= $dbManager->getByUniqueId('friendly_name', 'users', $userId).getTranslatedString('actions', 1).getTranslatedString('actions', 6).getTranslatedString('actions', 7).$dbManager->getByUniqueId('friendly_name', 'snacks', $snackId).getTranslatedString('actions', 8).$edit['old-i-value'].getTranslatedString('actions', 9).$edit['new-i-value'].'.';
                    $decodedEdits[] = $editSentence;
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
                $decodedActions[] = $action['created-at'].':'.getTranslatedString('actions', 10).getTranslatedString('user', 1).' '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).'.';
                break;
            case 2:
                $decodedEdits = decodeEdits('user', $action['id'], $action['user-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 3:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 11).$action['funds-amount'].' €.';
                break;
            case 4:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 10).getTranslatedString('snack', 2).' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 5:
                $decodedEdits = decodeEdits('snack', $action['id'], $action['user-id'], $action['snack-id']);
                foreach($decodedEdits as $decodedEdit) {
                    $decodedActions[] = $action['created-at'].': '.$decodedEdit;
                }
                break;
            case 6:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 12).$action['snack-quantity'].' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
            case 7:
                $decodedActions[] = $action['created-at'].': '.$dbManager->getByUniqueId('friendly_name', 'users', $action['user-id']).getTranslatedString('actions', 13).$action['snack-quantity'].' '.$dbManager->getByUniqueId('friendly_name', 'snacks', $action['snack-id']).'.';
                break;
        }
    }
    return $decodedActions;
}
