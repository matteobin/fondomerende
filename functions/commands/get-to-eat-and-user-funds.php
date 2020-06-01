<?php
require BASE_DIR_PATH.'functions/commands/get-user-funds.php';
function getToEatAndUserFunds($userId) {
    global $dbManager;
    $userFundsAmount = getUserFunds($userId, false);
    $snacks = array();
    $dbManager->query('SELECT snacks_stock.snack_id, snacks.friendly_name, snacks_stock.quantity, (SELECT crates.expiration FROM crates WHERE crates.snack_id=snacks_stock.snack_id AND crates.snack_quantity!=? ORDER BY crates.expiration ASC LIMIT 1) as expiration FROM snacks_stock JOIN snacks ON snacks_stock.snack_id=snacks.id WHERE snacks_stock.quantity!=? ORDER BY expiration ASC', array(0, 0), 'ii');
    while ($snacksStockRow = $dbManager->result->fetch_assoc()) {
        $snacks[] = array('id'=>$snacksStockRow['snack_id'], 'friendly-name'=>$snacksStockRow['friendly_name'], 'quantity'=>$snacksStockRow['quantity'], 'expiration'=>$snacksStockRow['expiration']);
    }
    foreach ($snacks as &$snack) {
        $dbManager->query('SELECT price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=? ORDER BY expiration ASC LIMIT 1', array($snack['id'], 0), 'ii');
        while ($row = $dbManager->result->fetch_assoc()) {
            $snack['price-per-snack'] = $row['price_per_snack'];
        }
    }
    $response['success'] = true;
    if (empty($snacks)) {
        $response['status'] = 404;
    } else {
        $response['status'] = 200;
    }
    $response['data'] = array('user-funds-amount'=>$userFundsAmount, 'snacks'=>$snacks);
    return $response;
}
