<?php
function deposit($userId, $amount) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES inflows WRITE, users_funds WRITE, fund_funds WRITE, actions WRITE');
        $dbManager->runPreparedQuery('INSERT INTO inflows (user_id, amount) VALUES (?,?)', array($userId, $amount), 'id');
        $dbManager->runQuery('SELECT id FROM inflows ORDER BY id DESC LIMIT 1');
        $inflowId = $dbManager->getQueryRes()->fetch_row()[0];
        $dbManager->runPreparedQuery('UPDATE users_funds SET amount=amount+? WHERE user_id=?', array($amount, $userId), 'di');
        $dbManager->runPreparedQuery('UPDATE fund_funds SET amount=amount+?', array($amount), 'd');
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, funds_amount, inflow_id) VALUES (?,?,?,?)', array($userId, 3, $amount, $inflowId), 'iidi');
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>200);
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
