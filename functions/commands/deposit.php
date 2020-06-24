<?php
require COMMANDS_PATH.'execute-deposit-or-withdraw-queries.php';
function deposit($userId, $amount) {
    global $dbManager;
    executeDepositOrWithdrawQueries($userId, $amount);
    return ['success'=>true, 'status'=>200];
}
