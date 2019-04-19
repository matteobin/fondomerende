<?php
    require_once('process-request.php');    
    if (isset($_POST['command-name']) && $_POST['command-name']=='edit-snack' && isset($response['response']['status']) && $response['response']['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
?>
    <h2><?php echoUcfirstTranslatedString('main', 22) ?> <?php echoTranslatedString('add-snack', 1); ?></h2>
</header>
<?php if (isset($response['response']['message'])): ?> 
    <p><?php echo($response['response']['message']); ?></p>
<?php endif; ?>
<form action="<?php echo(BASE_DIR); ?>index.php?view=edit-snack&command-name=get-snack-data&snack-name=<?php echo($snackName) ?>" method="POST">
    <input type="hidden" name="command-name" value="edit-snack">
    <input type="hidden" name="id" value="<?php if (isset($_POST['id'])) {echo($_POST['id']);} else {echo($response['data']['snack']['id']);} ?>">
    <label for="snack-name-input"><?php echoUcfirstTranslatedString('add-snack', 2); ?></label>
    <input type="text" name="name" id="snack-name-input" value="<?php if (isset($_POST['name'])) {echo($_POST['name']);} else {echo($response['data']['snack']['friendly-name']);} ?>" required>
    <label for="price-input"><?php echoUcfirstTranslatedString('add-snack', 3); ?></label>
    <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="99.99" placeholder="0.07" value="<?php if (isset($_POST['price'])) {echo($_POST['price']);} else {echo($response['data']['snack']['price']);} ?>" required>
    <label for="snacks-per-box-input"><?php echoUcfirstTranslatedString('add-snack', 4); ?></label>
    <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="99" placeholder="7" value="<?php if (isset($_POST['snacks-per-box'])) {echo($_POST['snacks-per-box']);} else {echo($response['data']['snack']['snacks-per-box']);} ?>" required>
    <label for="expiration-in-days-input"><?php echoUcFirstTranslatedString('add-snack', 5); ?></label>
    <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php if (isset($_POST['expiration-in-days'])) {echo($_POST['expiration-in-days']);} else {echo($response['data']['snack']['expiration-in-days']);} ?>" required>
    <input type="submit" value="<?php echoUcfirstTranslatedString('edit-user', 9); ?>">
</form>
<script>
    function askEditSnackConfirm(event) {
        event.preventDefault();
        if (confirm('Edit snack '+event.target[2].value+'?\n\nPrice: '+event.target[3].value+' €.\nSnacks per box: '+event.target[4].value+'.\nExpiration in days: '+event.target[5].value+'.')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askEditSnackConfirm);
</script>
