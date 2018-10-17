<pre>
<?php
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/lib/dbmanager.php');
require_once(__ROOT__.'/commands.php');
$userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
$command = filter_input(INPUT_POST, 'command', FILTER_SANITIZE_STRING);
$snackId = filter_input(INPUT_POST, 'snack-id', FILTER_SANITIZE_NUMBER_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
$dbManager = new DbManager('127.0.0.1', 'root', '', 'fondomerende');
if ($command!='') {
    if ($command=='eat') {
        var_dump(eat($userId, $snackId, $quantity));
    } else if ($command=='buy') {
        var_dump(buy($userId, $snackId, $quantity, array()));
    }
}
?>
</pre>
