<pre>
    <?php
        define('__ROOT__', dirname(__FILE__));
        require_once(__ROOT__.'/lib/dbmanager.php');
        require_once(__ROOT__.'/commands.php');
        $userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
        $commandId = filter_input(INPUT_POST, 'command-id', FILTER_SANITIZE_STRING);
        $snackId = filter_input(INPUT_POST, 'snack-id', FILTER_SANITIZE_NUMBER_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $dbManager = new DbManager('127.0.0.1', 'root', '', 'fondomerende');
        switch ($commandId) {
            case '1':
                var_dump(eat($userId, $snackId, $quantity));
                break;
            case '2':
                var_dump(buy($userId, $snackId, $quantity, array()));
                break;
            case '3':
                var_dump(deposit($userId, $amount));
                break;
        }
    ?>
</pre>
