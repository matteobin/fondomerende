<?php
function getFundFunds($apiCall=true) {
    global $dbManager;
    $dbManager->query('SELECT amount FROM fund_funds');
    $fundFundsAmount = $dbManager->result->fetch_row()[0];
    if ($apiCall) {
        return array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>$fundFundsAmount));
    } else {
        return $fundFundsAmount;
    }
}
