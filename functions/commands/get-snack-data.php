<?php
function getSnackData(DbManager $dbManager, $snackIdOrName) {
    if ((int)$snackIdOrName) {
        $snackId = $snackIdOrName;
    } else {
        $dbManager->query('SELECT id FROM snacks WHERE name=?', array($snackIdOrName), 's'); 
        $snackId = $dbManager->result->fetch_row()[0]; 
    }
    $dbManager->query('SELECT id, friendly_name, price, snacks_per_box, expiration_in_days, visible FROM snacks WHERE id=?', array($snackId), 'i');
    while ($row = $dbManager->result->fetch_assoc()) {
        $snack = array('id'=>$row['id'], 'friendly-name'=>$row['friendly_name'], 'price'=>$row['price'], 'snacks-per-box'=>$row['snacks_per_box'], 'expiration-in-days'=>$row['expiration_in_days'], 'visible'=>$row['visible']);
    }
    return array('success'=>true, 'status'=>200, 'data'=>array('snack'=>$snack));
}
