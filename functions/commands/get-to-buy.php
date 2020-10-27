<?php
function getToBuy(DbManager $dbManager) {
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction(MYSQLI_TRANS_START_READ_ONLY);
    }
    $dbManager->query('SELECT id, friendly_name, price, snacks_per_box, expiration_in_days FROM snacks WHERE visible=? ORDER BY friendly_name ASC', array(1), 'i');
    $snacks = array();
    while ($row = $dbManager->result->fetch_assoc()) {
        $snacks[] = array('id'=>$row['id'], 'friendly_name'=>$row['friendly_name'], 'price'=>$row['price'], 'snacks-per-box'=>$row['snacks_per_box'], 'expiration'=>(new DateTime('+'.$row['expiration_in_days'].' days'))->format('Y-m-d'));
    }
    return array('success'=>true, 'status'=>(empty($snacks)?404:200), 'data'=>array('snacks'=>$snacks));
}
