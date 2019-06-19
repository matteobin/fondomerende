<?php
function getLatestActions($fromTime) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runPreparedQuery('SELECT * FROM actions WHERE created_at>? ORDER BY created_at DESC', array($fromTime), 's');
        $dbManager->endTransaction();
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
}
