<?php
function getOldValues(DbManager $dbManager, array $newValues, $table, $whereColumn, $whereId, array $exceptions=null) {
    $oldValues = array();
    foreach($newValues as $column=>$newValue) {
        if (!isset($exceptions[$column])) {
            $dbManager->query('SELECT '.$column.' FROM '.$table.' WHERE '.$whereColumn.'=?', array($whereId), 'i');
            while ($row = $dbManager->result->fetch_assoc()) {
                $oldValues[$column] = $row[$column];
            }
        }
    }
    return $oldValues;
}
function updateQuery(DbManager $dbManager, $table, array $newValues, array $paramTypesArray, $whereColumn, $whereId, array $oldValues=null) {
    $query = 'UPDATE '.$table.' SET ';
    $params = array();
    $paramTypes = '';
    foreach ($newValues as $column=>$newValue) {
        if (isset($oldValues[$column])) {
            if ($newValue!=$oldValues[$column]) {
                if ($paramTypes=='') {
                    $query .= $column.'=?';
                } else {
                    $query .= ', '.$column.'=?';
                }
                $params[] = $newValue;
                if (isset($paramTypesArray[$column])) {
                    $paramTypes .= $paramTypesArray[$column];
                } else {
                    $backtrace = debug_backtrace();
                    throw new Exception($column.' type is missing in types array at line '.$backtrace[1]['line'].' in '.$backtrace[1]['file'].'.');
                }
            }
        } else {
            if ($paramTypes=='') {
                $query .= $column.'=?';
            } else {
                $query .= ', '.$column.'=?';
            }
            $params[] = $newValue;
            $paramTypes .= $paramTypesArray[$column];
        }
    }
    if ($paramTypes!='') {
        $query .= ' WHERE '.$whereColumn.'=?';
        $params[] = $whereId;
        $paramTypes .= 'i';
        $dbManager->query($query, $params, $paramTypes);
        return true;
    } else return false;
}
function insertEdits(DbManager $dbManager, $newValues, $types, $oldValues) {
    $dbManager->query('SELECT id FROM actions ORDER BY id DESC LIMIT 1');
    $actionId = 0;
    while ($row = $dbManager->result->fetch_row()) {
        $actionId = $row[0];
    }
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
function editSnackOrUser(DbManager $dbManager, array $ids, array $newValues, array $types) {
    if (isset($ids['snack'])) {
        $table = 'snacks';
        $whereId = $ids['snack'];
        $oldValueCheckExceptions = null;
    } else {
        $table = 'users';
        $whereId = $ids['user'];
        $oldValueCheckExceptions = ['password'];
    }
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction();
    }
    $oldValues = getOldValues($dbManager, $newValues, $table, 'id', $whereId, $oldValueCheckExceptions);
    if (updateQuery($dbManager, $table, $newValues, $types, 'id', $whereId, $oldValues)) {
        if ($table=='snacks') {
            $dbManager->query('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($ids['user'], 6, $ids['snack']), 'iii');
        } else {
            $dbManager->query('INSERT INTO actions (user_id, command_id) VALUES (?, ?)', array($ids['user'], 2), 'ii');
        }
        insertEdits($dbManager, $newValues, $types, $oldValues);
    }
    if ($table=='users' && isset($newValues['friendly_name']) && $newValues['friendly_name']!=$oldValues['friendly_name']) {
        $_SESSION['user-friendly-name'] = $newValues['friendly_name'];
    }
    return array('success'=>true, 'status'=>200);
}
