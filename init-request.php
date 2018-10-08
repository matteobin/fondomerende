<pre>
<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/lib/php/DbManager.php');
require_once(__ROOT__.'/commands.php');
$userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
$command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_STRING);
$snackId = filter_input(INPUT_POST, 'snack-id', FILTER_SANITIZE_NUMBER_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
$dbManager = new DbManager('127.0.0.1', 'root', '', 'fondomerende');
if ($command!='') {
    var_dump($userId);
    var_dump($command);
    var_dump($snackId);
    var_dump($quantity);
    eat($userId, $snackId, $quantity);
}
?>
</pre>
