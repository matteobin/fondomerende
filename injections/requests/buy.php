<?php
while (true) {
    if ((API_REQUEST && ((require FUNCTIONS_PATH.'check-request-method.php')&&!checkRequestMethod('POST', $response)||(require FUNCTIONS_PATH.'check-token.php')&&!checkToken($response, $dbManager)) || (require FUNCTIONS_PATH.'check-user-active.php') && !checkUserActive($bManager, $response)) {
        break;
    }
    if (!setRequestInputValue($snackId, true, 'id', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'database'=>array('table'=>'snacks', 'select-column'=>'id', 'value-type'=>'i', 'check-type'=>'existence')))) {
        break;
    }
    if (!setRequestInputValue($quantity, true, 'quantity', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>1000))) {
        break;
    }
    $customiseBuyOptions = false;
    if (!API_REQUEST) {
        if (!setRequestInputValue($customiseBuyOptions, false, 'customise-buy-options', array('filter'=>FILTER_VALIDATE_BOOLEAN, 'options'=>array('flags'=>FILTER_NULL_ON_FAILURE)), array())) {
            break;
        }
    }
    $options = array();
    if (API_REQUEST || $customiseBuyOptions) {
        if (!setRequestInputValue($options, false, 'price', array('filter'=>FILTER_VALIDATE_FLOAT), array('greater-than'=>0, 'digits-number'=>4, 'decimals-number'=>2, 'less-than'=>100))) {
            break;
        }
        if (!setRequestInputValue($options, false, 'snacks-per-box', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>1000))) {
            break;
        }
        if (!setRequestInputValue($options, false, 'expiration-in-days', array('filter'=>FILTER_VALIDATE_INT), array('greater-than'=>0, 'less-than'=>10000))) {
            break;
        }
        if (!setRequestInputValue($expiration, false, 'expiration', array('filter'=>FILTER_SANITIZE_STRING), array('date'=>array('greater-than'=>(new DateTime('-1 days')), 'less-than'=>(new DateTime('+10000 days')))))) {
            break;
        }
        if (isset($expiration)) {
            $options['expiration_in_days'] = ((new DateTime('today'))->diff(new DateTime($expiration)))->days;
        }
    }
    require COMMANDS_PATH.'buy.php';
    $response = buy($dbManager, $_SESSION['user-id'], $snackId, $quantity, $options);
    break;
}
