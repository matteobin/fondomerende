<?php
	$noSnacksToBuy = false;
	require 'process-request.php';
    if (isset($_POST['command-name']) && $_POST['command-name']=='buy' && isset($response['status']) && $response['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
	if (isset($response['data']['snacks'])) {
		$snacks = $response['data']['snacks'];
        $_SESSION['buy-form-data']['snacks'] = $snacks;
    } else if ($response['status']==404) {
		$noSnacksToBuy = true;
	} else {
        $snacks = $_SESSION['buy-form-data']['snacks'];
    }
?>
    <h2><?php echoUcfirstTranslatedString('commands', 4); ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
    <p><?php echo $response['message']; ?></p>
<?php endif; ?>
<?php if ($noSnacksToBuy): ?>
    <h3><?php echoTranslatedString('commons', 5); ?> <?php echoTranslatedString('commands', 4); ?>!</h3>
    <p><?php echoTranslatedString('commons', '6'); ?><a href="<?php echo BASE_DIR; ?>index.php?view=add-snack"><strong><?php echoStrtoupperTranslatedString('commands', 1); ?></strong></a><?php echoTranslatedString('commons', 7) ?></p>
<?php else: ?>
	<form method="post">
        <input type="hidden" name="command-name" value="buy">
        <select name="id" required>
            <?php foreach($snacks as $snack): ?>
                <option value="<?php echo $snack['id']; ?>"<?php if (isset($_POST['id']) && $_POST['id']==$snack['id']) {echo 'selected';} ?>><?php echo $snack['friendly_name']; ?></option>
            <?php endforeach; $_SESSION['snacks'] = $snacks; ?> 
        </select>
        <label for="quantity-input"><?php echoTranslatedString('buy', 2); ?></label>
        <input type="number" id="quantity-input" name="quantity" min="1" step="1" max="999" placeholder="1" value="<?php if (isset($_POST['quantity'])) {echo $_POST['quantity'];} ?>" required>
        <label for="customise-buy-options-input"><?php echoTranslatedString('buy', 3); ?></label>
        <input type="checkbox" id="customise-buy-options-input" name="customise-buy-options" value="yes" <?php if (isset($_POST['customise-buy-options']) && $_POST['customise-buy-options']=='yes') {echo 'checked';} ?>>
        <label for="price-input"><?php echoUcfirstTranslatedString('snack', 3); ?></label>
        <input type="number" id="price-input" name="price" min="0.01" step="0.01" max="99.99" value="<?php if (isset($_POST['price'])) {echo $_POST['price'];} ?>">
        <label for="snacks-per-box-input"><?php echoUcfirstTranslatedString('snack', 4); ?></label>
        <input type="number" id="snacks-per-box-input" name="snacks-per-box" min="1" step="1" max="999" value="<?php if (isset($_POST['snacks-per-box'])) {echo $_POST['snacks-per-box'];} ?>">
        <label for="expiration-in-days-input"><?php echoTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?></label>
        <input type="number" id="expiration-in-days-input" name="expiration-in-days" min="1" step="1" max="9999" value="<?php if (isset($_POST['expiration-in-days'])) {echo $_POST['expiration-in-days'];} ?>">
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
<?php endif; ?>
