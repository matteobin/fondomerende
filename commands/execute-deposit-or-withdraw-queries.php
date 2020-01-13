<?php
function executeDepositOrWithdrawQueries($userId, $amount, $isDeposit=true) {
    global $dbManager;
    if ($isDeposit) {
        $flowWay = 'in';
        $operator = '+';
        $commandId = 3;
    } else {
        $flowWay = 'out';
        $operator = '-';
        $commandId = 4;
    }
    if ($isDeposit) {
        $dbManager->runQuery('LOCK TABLES inflows WRITE, users_funds WRITE, fund_funds WRITE, actions WRITE');
    }
    $dbManager->runPreparedQuery('INSERT INTO '.$flowWay.'flows (user_id, amount) VALUES (?,?)', array($userId, $amount), 'id');
    $dbManager->runQuery('SELECT id FROM '.$flowWay.'flows ORDER BY id DESC LIMIT 1');
    $flowId = $dbManager->getQueryRes()->fetch_row()[0];
    $dbManager->runPreparedQuery('UPDATE users_funds SET amount=amount'.$operator.'? WHERE user_id=?', array($amount, $userId), 'di');
    $dbManager->runPreparedQuery('UPDATE fund_funds SET amount=amount'.$operator.'?', array($amount), 'd');
    $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, funds_amount, '.$flowWay.'flow_id) VALUES (?,?,?,?)', array($userId, $commandId, $amount, $flowId), 'iidi');
    if ($isDeposit) {
        $dbManager->runQuery('UNLOCK TABLES');
    }
}
