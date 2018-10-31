<pre>
    <?php
    define('__ROOT__', dirname(__FILE__));
    require_once(__ROOT__.'/lib/DbManager/DbManager.php');
    require_once(__ROOT__.'/commands.php');
    $userId = filter_input(INPUT_POST, 'user-id', FILTER_SANITIZE_NUMBER_INT);
    $commandId = filter_input(INPUT_POST, 'command-id', FILTER_SANITIZE_NUMBER_INT);
    $snackId = filter_input(INPUT_POST, 'snack-id', FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $snacksPerBox = filter_input(INPUT_POST, 'snacks-per-box', FILTER_SANITIZE_NUMBER_INT);
    $isLiquid = filter_input(INPUT_POST, 'is-liquid', FILTER_VALIDATE_BOOLEAN);
    $expirationInDays = filter_input(INPUT_POST, 'expiration-in-days', FILTER_SANITIZE_NUMBER_INT);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $friendlyName = filter_input(INPUT_POST, 'friendly-name', FILTER_SANITIZE_STRING);
    $dbManager = new DbManager();
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
        case '4':
            var_dump(addSnack($userId, $name, $price, $snacksPerBox, $isLiquid, $expirationInDays));
            break;
        case '5':
            var_dump(editSnackOrUser(array('user'=>$userId, 'snack'=>$snackId), array('name'=>$name, 'price'=>$price), array('name'=>'s', 'price'=>'d'), array('name'=>'Oreo', 'price'=>7.07)));
            break;
        case '6':
            var_dump(addUser($name, $password, $friendlyName));
            break;
        case '7':
            var_dump(editSnackOrUser(array('user'=>$userId), array('name'=>$name), array('name'=>'s'), array('name'=>'pk9rocco')));
            break;
    }
    ?>
</pre>
