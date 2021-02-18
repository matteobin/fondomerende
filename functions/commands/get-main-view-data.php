<?php
require COMMANDS_PATH.'get-fund-funds.php';
require COMMANDS_PATH.'get-user-funds.php';
require COMMANDS_PATH.'get-actions.php';
function getMainViewData(DbManager $dbManager, $userId) {
    return array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>getFundFunds($dbManager, false), 'user-funds-amount'=>getUserFunds($dbManager, $userId, false), 'actions'=>getActions($dbManager, false, 5, 0, 'DESC', false)));
}
