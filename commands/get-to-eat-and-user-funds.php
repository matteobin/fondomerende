<?php
require 'get-user-funds.php';
function getToEatAndUserFunds($userId) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES users_funds READ, snacks_stock READ, crates READ, snacks READ');
		$userFundsAmount = getUserFunds($userId, false);
		$snacks = array();
        $dbManager->runPreparedQuery('SELECT snacks_stock.snack_id, snacks.friendly_name, snacks_stock.quantity, (SELECT crates.expiration FROM crates WHERE crates.snack_id=snacks_stock.snack_id AND crates.snack_quantity!=? ORDER BY crates.expiration ASC LIMIT 1) as expiration FROM snacks_stock JOIN snacks ON snacks_stock.snack_id=snacks.id WHERE snacks_stock.quantity!=? ORDER BY expiration ASC', array(0, 0), 'ii');
        while ($snacksStockRow = $dbManager->getQueryRes()->fetch_assoc()) {
			$snacks[] = array('id'=>$snacksStockRow['snack_id'], 'friendly-name'=>$snacksStockRow['friendly_name'], 'quantity'=>$snacksStockRow['quantity'], 'expiration'=>$snacksStockRow['expiration']);
		}
		foreach ($snacks as &$snack) {
			$dbManager->runPreparedQuery('SELECT price_per_snack FROM crates WHERE snack_id=? AND snack_quantity!=? ORDER BY expiration ASC LIMIT 1', array($snack['id'], 0), 'ii');
			while ($cratesRow = $dbManager->getQueryRes()->fetch_assoc()) {
				$snack['price-per-snack'] = $cratesRow['price_per_snack'];
			}
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        $response['success'] = true;
        if (empty($snacks)) {
            $response['status'] = 404;
        } else {
            $response['status'] = 200;
        }
        $response['data'] = array('user-funds-amount'=>$userFundsAmount, 'snacks'=>$snacks);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
