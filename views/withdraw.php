<?php
require 'process-request.php';
$fundsTypeLabel = getTranslatedString('commons', 1);
if (isset($_POST['funds-amount'])) {
    $funds = $_POST['funds-amount'];
} else {
    $funds = $response['data']['fund-funds-amount'];
}
if ($funds<=0) {
    $maxAmount = 0;
    $response['status'] = 404;
} else {
    $maxAmount = $funds;
}
$commandName = 'withdraw';
if (isset($_POST['amount'])) {
    $amount = $_POST['amount'];
}
require '../views/deposit-or-withdraw.php';
