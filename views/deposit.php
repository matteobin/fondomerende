<?php
require BASE_DIR_PATH.'public/process-request.php';
$fundsTypeLabel = getTranslatedString('commons', 2);
if (isset($_POST['funds-amount'])) {
    $funds = $_POST['funds-amount'];
} else {
    $funds = $response['data']['user-funds-amount'];
}
$commandName = 'deposit';
$maxAmount = 99.99;
if (isset($_POST['amount'])) {
    $amount = $_POST['amount'];
}
require BASE_DIR_PATH.'views/deposit-or-withdraw.php';
