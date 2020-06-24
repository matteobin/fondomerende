<?php
require COMMANDS_PATH.'execute-deposit-or-withdraw-queries.php';
function withdraw($userId, $amount) {
    global $dbManager;
    $dbManager->query('SELECT amount FROM fund_funds');
    $fundAmount = 0;
    while ($row = $dbManager->result->fetch_row()) {
        $fundAmount = $row[0];
    }
    $response['success'] = true;
    if ($amount>$fundAmount) {
        $response['status'] = 404;
        $response['message'] = getTranslatedString('response-messages', 29).$amount.getTranslatedString('response-messages', 30);
    } else {
        executeDepositOrWithdrawQueries($userId, $amount, false);
        $response['status'] = 200;
    }
    return $response;
}
