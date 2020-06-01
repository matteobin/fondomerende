<?php
require BASE_DIR_PATH.'functions/commands/get-fund-funds.php';
require BASE_DIR_PATH.'functions/commands/get-user-funds.php';
require BASE_DIR_PATH.'functions/commands/get-actions.php';
function getMainViewData($userId) {
    global $dbManager;
    return array('success'=>true, 'status'=>200, 'data'=>array('fund-funds-amount'=>getFundFunds(false), 'user-funds-amount'=>getUserFunds($userId, false), 'actions'=>getActions(false, 5, 0, 'DESC', false)));
}
