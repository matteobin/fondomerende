<?php
	require_once('process-request.php');
    if (isset($_POST['command-name']) && $_POST['command-name']=='buy' && isset($response['response']['status']) && $response['response']['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
	if (isset($response['data']['snacks'])) {
		$snacks = $response['data']['snacks'];
        $_SESSION['buy-form-data']['snacks'] = $snacks;
    } else {
        $snacks = $_SESSION['buy-form-data']['snacks'];
    }
?>
    <h2><?php echoUcfirstTranslatedString('commands', 4); ?></h2>
</header>
<?php if (isset($response['response']['message'])): ?> 
    <p><?php echo($response['response']['message']); ?></p>
<?php endif; ?>
<form action="<?php echo(BASE_DIR); ?>index.php?view=buy&command-name=get-to-buy-and-fund-funds" method="POST">
    <input type="hidden" name="command-name" value="buy">
    <input type="hidden" name="fund-funds-amount" value="<?php if (isset($_POST['fund-funds-amount'])) {echo($_POST['fund-funds-amount']);} else {echo($response['data']['fund-funds-amount']);} ?>">
    <select name="id" required>
        <?php foreach($snacks as $snack): ?>
            <option value="<?php echo($snack['id']); ?>"<?php if (isset($_POST['id']) && $_POST['id']==$snack['id']) {echo('selected');} ?>><?php echo($snack['friendly_name']); ?></option>
        <?php endforeach; $_SESSION['snacks'] = $snacks; ?> 
    </select>
    <label><?php echoTranslatedString('buy', 2); ?>
        <input type="number" name="quantity" min="1" step="1" max="999" placeholder="1" value="<?php if (isset($_POST['quantity'])) {echo($_POST['quantity']);} ?>" required>
    </label>
    <label><?php echoTranslatedString('buy', 3); ?>
        <input type="checkbox" name="customise-buy-options" value="yes" <?php if (isset($_POST['customise-buy-options']) && $_POST['customise-buy-options']=='yes') {echo('checked');} ?>>
    </label>
    <label><?php echoUcfirstTranslatedString('snack', 3); ?>
        <input type="number" name="price" min="0.01" step="0.01" max="999.99" value="<?php if (isset($_POST['price'])) {echo($_POST['price']);} ?>">
    </label>
    <label><?php echoUcfirstTranslatedString('snack', 4); ?>
        <input type="number" name="snacks-per-box" min="1" step="1" max="999" value="<?php if (isset($_POST['snacks-per-box'])) {echo($_POST['snacks-per-box']);} ?>">
    </label>
    <label><?php echoTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?>
        <input type="number" name="expiration-in-days" min="1" step="1" max="9999" value="<?php if (isset($_POST['expiration-in-days'])) {echo($_POST['expiration-in-days']);} ?>">
    </label>
    <input type="submit" value="<?php echoUcfirstTranslatedString('commands', 4); ?>">
</form>
<script>
    function askBuyConfirm(event) {
        event.preventDefault();
        var cratesNumber = event.target[3].value;
        var cratesString = " <?php echoTranslatedString('buy', 5); ?>";
        if (cratesNumber=='1') {
            cratesString = " <?php echoTranslatedString('buy', 4); ?>";
        }
        var confirmString = '<?php echoUcfirstTranslatedString('commands', 4); ?> '+cratesNumber+cratesString+' <?php echoTranslatedString('buy', 6); ?> '+event.target[2][event.target[2].selectedIndex].innerText+'?';
        console.log(event.target);
        if (event.target[4].checked) {
            confirmString += '\n\n<?php echoUcfirstTranslatedString('snack', 3); ?>: '+event.target[5].value+' €.\n<?php echoUcfirstTranslatedString('snack', 4); ?>: '+event.target[6].value+'. \n<?php echoTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?>: '+event.target[7].value+'.';
        }
        if (confirm(confirmString)) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askBuyConfirm);
</script>
