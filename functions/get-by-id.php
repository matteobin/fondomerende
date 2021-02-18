<?php
function getById(DbManager $dbManager, $column, $table, $id) {
    $dbManager->query('SELECT '.$column.' FROM '.$table.' WHERE id=?', array($id), 'i');
    $value = null;
    while ($row = $dbManager->result->fetch_assoc()) {
        $value = $row[$column];
    }
    return $value;
}