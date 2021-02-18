<?php
function getFundFunds(DbManager $dbManager, $apiCall=true) {
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction(true);
    }
    $dbManager->query('SELECT amount FROM fund_funds');
    $fundFundsAmount = 0;
    while ($row = $dbManager->result->fetch_row()) {
        $fundFundsAmount = $row[0];
    }
    if ($apiCall) {
        return array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>$fundFundsAmount));
    } else {
        return $fundFundsAmount;
    }
}
