<?php
function executeDepositOrWithdrawQueries(DbManager $dbManager, $userId, $amount, $isDeposit=true) {
    if ($isDeposit) {
        $flowWay = 'in';
        $operator = '+';
        $commandId = 3;
    } else {
        $flowWay = 'out';
        $operator = '-';
        $commandId = 4;
    }
    $dbManager->query('INSERT INTO '.$flowWay.'flows (user_id, amount) VALUES (?, ?)', array($userId, $amount), 'id');
    $dbManager->query('SELECT id FROM '.$flowWay.'flows ORDER BY id DESC LIMIT 1');
    $flowId = $dbManager->result->fetch_row()[0];
    $dbManager->query('UPDATE users_funds SET amount=amount'.$operator.'? WHERE user_id=?', array($amount, $userId), 'di');
    $dbManager->query('UPDATE fund_funds SET amount=amount'.$operator.'?', array($amount), 'd');
    $dbManager->query('INSERT INTO actions (user_id, command_id, funds_amount, '.$flowWay.'flow_id) VALUES (?, ?, ?, ?)', array($userId, $commandId, $amount, $flowId), 'iidi');
}
