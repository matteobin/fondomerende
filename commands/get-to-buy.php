<?php
function getToBuy() {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('SELECT id, friendly_name, price, snacks_per_box, expiration_in_days FROM snacks ORDER BY friendly_name ASC');
        while ($snacksRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $snacks[] = array('id'=>$snacksRow['id'], 'friendly_name'=>$snacksRow['friendly_name'], 'price'=>$snacksRow['price'], 'snacks-per-box'=>$snacksRow['snacks_per_box'], 'expiration-in-days'=>$snacksRow['expiration_in_days']);
        }
        $dbManager->endTransaction();
        $response['success'] = true;
        if (isset($snacks)) {
            $respose['status'] = 200;
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
