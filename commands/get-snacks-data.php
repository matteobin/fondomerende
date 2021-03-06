<?php
function getSnacksData() {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES snacks READ');
        $dbManager->runQuery('SELECT id, name, friendly_name, price, snacks_per_box, expiration_in_days, visible FROM snacks ORDER BY friendly_name ASC');
        $snacks = array();
        while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $snacks[] = array('id'=>$snacksRow['id'], 'name'=>$snacksRow['name'], 'friendly-name'=>$snacksRow['friendly_name'], 'price'=>$snacksRow['price'], 'snacks-per-box'=>$snacksRow['snacks_per_box'], 'expiration-in-days'=>$snacksRow['expiration_in_days'], 'visible'=>$snacksRow['visible']==1);
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        $response['success'] = true; 
        if (empty($snacks)) {
            $response['status'] = 404;
        } else {
            $response['status'] = 200;
        }
        $response['data']['snacks'] = $snacks;

    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
