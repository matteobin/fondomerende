<?php
$isException = false;
if (isset($options['database']['exceptions'])) {
    foreach ($options['database']['exceptions'] as $exception) {
        if ($value==$exception) {
            $isException = true;
            break;
        }
    }
}
if (!$isException) {
    global $dbManager;
    $selectColumn = $options['database']['select-column'];
    $table = $options['database']['table'];
    $valueType = $options['database']['value-type'];
    $query = 'SELECT '.$selectColumn.' FROM '.$table.' WHERE '.$selectColumn.'=?';
    $params[] = $value;
    $types = $valueType;
    $additionalWheres = false;
    if (isset($options['database']['wheres'])) {
        $additionalWheres = true;
        foreach($options['database']['wheres'] as $where) {
            $query .= ' AND '.$where['column'].'=?';
            $params[] = $where['value'];
            $types .= $where['type'];
        }
    }
    $checkType = $options['database']['check-type'];
    if ($checkType=='insert-unique') {
        $insertUnique = true; 
    } else {
        $insertUnique = false;
    }
    if (!$dbManager->transactionBegun) {
        $readTransaction = isset($options['database']['read-transaction']) && $options['database']['read-transaction'] ? true : false;
        $dbManager->beginTransaction($readTransaction);
    }
    $dbManager->query($query, $params, $types);
    $dbValue = null;
    while ($row = $dbManager->result->fetch_assoc()) {
        $dbValue = $row[$selectColumn];
    }
    if ($insertUnique && $dbValue!=null) {
        $valid = false;
        $message = $value.''.getTranslatedString('response-messages', 17).$table.getTranslatedString('response-messages', 18).$selectColumn.getTranslatedString('response-messages', 19);
    } else if (!$insertUnique && $dbValue===null) {
        $valid = false;
        $message = $value.''.getTranslatedString('response-messages', 20).$table.getTranslatedString('response-messages', 18).$selectColumn.getTranslatedString('response-messages', 19);
        if ($additionalWheres) {
            foreach($options['database']['wheres'] as $where) {
                $message .= getTranslatedString('response-messages', 21).$where['column'].getTranslatedString('response-messages', 22).$where['value'];
            }
            $message .= '.';
        }
    } 
}
