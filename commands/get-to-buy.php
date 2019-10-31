<?php
function getToBuy() {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES snacks READ');
        $dbManager->runPreparedQuery('SELECT id, friendly_name, price, snacks_per_box, expiration_in_days FROM snacks WHERE visible=? ORDER BY friendly_name ASC', array(1), 'i');
        while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $snacks[] = array('id'=>$snacksRow['id'], 'friendly_name'=>$snacksRow['friendly_name'], 'price'=>$snacksRow['price'], 'snacks-per-box'=>$snacksRow['snacks_per_box'], 'expiration'=>date('Y-m-d', strtotime('+'.$snacksRow['expiration_in_days'].' days')));
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        $response['success'] = true;
        if (isset($snacks)) {
            $response['status'] = 200;
            $response['data']['snacks'] = $snacks;
        } else {
            $response['status'] = 404;
        }
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
