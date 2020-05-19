<?php
function getSnacksData() {
    global $dbManager;
    try {
        $dbManager->query('SELECT id, name, friendly_name, price, snacks_per_box, expiration_in_days, visible FROM snacks ORDER BY friendly_name ASC');
        $snacks = array();
        while ($row = $dbManager->result->fetch_assoc()) {
            $snacks[] = array('id'=>$row['id'], 'name'=>$row['name'], 'friendly-name'=>$row['friendly_name'], 'price'=>$row['price'], 'snacks-per-box'=>$row['snacks_per_box'], 'expiration-in-days'=>$row['expiration_in_days'], 'visible'=>$row['visible']==1);
        }
        $response['success'] = true; 
        $response['status'] = empty($snacks) ? 404 : 200;
        $response['data']['snacks'] = $snacks;

    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
