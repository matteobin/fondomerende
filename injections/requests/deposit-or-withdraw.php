<?php
if ((!API_REQUEST || (require FUNCTIONS_PATH.'check-request-method.php')&&checkRequestMethod('POST', $response)&&(require FUNCTIONS_PATH.'check-token.php')&&checkToken($response, $dbManager)) && (require FUNCTIONS_PATH.'check-user-active.php') && checkUserActive($dbManager, $response)) {
   if (setRequestInputValue($amount, true, 'amount', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2))) {
        if ($commandName=='deposit') {
            require COMMANDS_PATH.'deposit.php';
            $response = deposit($dbManager, $_SESSION['user-id'], $amount);
        } else {
            require COMMANDS_PATH.'withdraw.php';
            $response = withdraw($dbManager, $_SESSION['user-id'], $amount);
        }
   }
}
