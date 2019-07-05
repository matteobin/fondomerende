<?php
function insertEdits($newValues, $types, $oldValues) {
    global $dbManager;
    $dbManager->runQuery('SELECT id FROM actions ORDER BY id DESC LIMIT 1');
    $actionId = $dbManager->getQueryRes()->fetch_assoc()['id'];
    foreach($newValues as $column=>$newValue) {
        $type = $types[$column];
        if (isset($oldValues[$column])) {
            if ($oldValues[$column]!=$newValue) {
                $dbManager->runPreparedQuery('INSERT INTO edits (action_id, column_name, old_'.$type.'_value, new_'.$type.'_value) VALUES (?, ?, ?, ?)', array($actionId, $column, $oldValues[$column], $newValue), 'is'.$type.$type);
            }
        } else {
            $dbManager->runPreparedQuery('INSERT INTO edits (action_id, column_name, new_'.$type.'_value) VALUES (?, ?, ?)', array($actionId, $column, $newValue), 'is'.$type);
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
        $dbManager->startTransaction();
        $oldValues = $dbManager->getOldValues($newValues, $table, 'id', $whereId, $oldValueCheckExceptions);
        if ($dbManager->runUpdateQuery($table, $newValues, $types, 'id', $whereId, $oldValues)) {
            if ($table=='snacks') {
                $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($ids['user'], 5, $ids['snack']), 'iii');
            } else {
                $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($ids['user'], 2), 'ii');
            }
            insertEdits($newValues, $types, $oldValues);
        }
        if ($table=='users' && isset($newValues['friendly_name']) && $newValues['friendly_name']!=$oldValues['friendly_name']) {
            $_SESSION['user-friendly-name'] = $newValues['friendly_name'];
            if (filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN)) {
                setcookie('user-friendly-name', $newValues['friendly_name'], time()+86400*5);
            } else {
                setcookie('user-friendly-name', $newValues['friendly_name'], 0);
            }
        }
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
