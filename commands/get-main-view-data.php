<?php
require 'get-fund-funds.php';
require 'get-user-funds.php';
require 'get-actions.php';
function getMainViewData($userId) {
    global $dbManager;
    return array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>getFundFunds(false), 'user-funds-amount'=>getUserFunds($userId, false), 'actions'=>getActions(false, 5, 0, 'DESC', false)));
}
