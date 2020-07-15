<?php
require COMMANDS_PATH.'execute-deposit-or-withdraw-queries.php';
function deposit(DbManager $dbManager, $userId, $amount) {
    executeDepositOrWithdrawQueries($dbManager, $userId, $amount);
    return array('success'=>true, 'status'=>200);
}
