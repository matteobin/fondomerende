<?php
if ((!API_REQUEST || checkRequestMethod('POST')&&checkToken()) && checkUserActive()) {
   if (setRequestInputValue($amount, true, 'amount', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2))) {
        if ($commandName=='deposit') {
            require BASE_DIR_PATH.'commands/deposit.php';
            $dbManager->lockTables(array('inflows'=>'w', 'users_funds'=>'w', 'fund_funds'=>'w', 'actions'=>'w'));
            $response = deposit($_SESSION['user-id'], $amount);
        } else {
            require BASE_DIR_PATH.'functions/commands/withdraw.php';
            $dbManager->lockTables(array('outflows'=>'w', 'users_funds'=>'w', 'fund_funds'=>'w', 'actions'=>'w'));
            $response = withdraw($_SESSION['user-id'], $amount);
        }
   }
}
