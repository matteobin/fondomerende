<?php
function insertEdits($newValues, $types, $oldValues) {
    global $dbManager;
    $dbManager->query('SELECT id FROM actions ORDER BY id DESC LIMIT 1');
    $actionId = $dbManager->result->fetch_assoc()['id'];
    foreach($newValues as $column=>$newValue) {
        $type = $types[$column];
        if (isset($oldValues[$column])) {
            if ($oldValues[$column]!=$newValue) {
                $dbManager->query('INSERT INTO edits (action_id, column_name, old_'.$type.'_value, new_'.$type.'_value) VALUES (?, ?, ?, ?)', array($actionId, $column, $oldValues[$column], $newValue), 'is'.$type.$type);
            }
        } else {
            $dbManager->query('INSERT INTO edits (action_id, column_name, new_'.$type.'_value) VALUES (?, ?, ?)', array($actionId, $column, $newValue), 'is'.$type);
        }
    }
}
function editSnackOrUser(array $ids, array $newValues, array $types) {
    global $dbManager;
    if (isset($ids['snack'])) {
        $table = 'snacks';
        $whereId = $ids['snack'];
        $oldValueCheckExceptions = null;
    } else {
        $table = 'users';
        $whereId = $ids['user'];
        $oldValueCheckExceptions = array('password');
    }
    try {
        $oldValues = $dbManager->getOldValues($newValues, $table, 'id', $whereId, $oldValueCheckExceptions);
        if ($dbManager->runUpdateQuery($table, $newValues, $types, 'id', $whereId, $oldValues)) {
            if ($table=='snacks') {
                $dbManager->query('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($ids['user'], 6, $ids['snack']), 'iii');
            } else {
                $dbManager->query('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($ids['user'], 2), 'ii');
            }
            insertEdits($newValues, $types, $oldValues);
        }
        if ($table=='users' && isset($newValues['friendly_name']) && $newValues['friendly_name']!=$oldValues['friendly_name']) {
            $_SESSION['user-friendly-name'] = $newValues['friendly_name'];
        }
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
