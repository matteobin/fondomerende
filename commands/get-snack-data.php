<?php
function getSnackData($snackIdOrName) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES snacks READ');
        if ((int)$snackIdOrName) {
            $snackId = $snackIdOrName;
        } else {
            $dbManager->runPreparedQuery('SELECT id FROM snacks WHERE name=?', array($snackIdOrName), 's'); 
            $snackId = $dbManager->getQueryRes()->fetch_row()[0]; 
        }
        $dbManager->runPreparedQuery('SELECT id, friendly_name, price, snacks_per_box, expiration_in_days, visible FROM snacks WHERE id=?', array($snackId), 'i');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $snack = array('id'=>$row['id'], 'friendly-name'=>$row['friendly_name'], 'price'=>$row['price'], 'snacks-per-box'=>$row['snacks_per_box'], 'expiration-in-days'=>$row['expiration_in_days'], 'visible'=>$row['visible']);
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200, 'data'=>array('snack'=>$snack));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
