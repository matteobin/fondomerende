<?php
	require_once('process-request.php');
	if (isset($response['data']['snacks'])) {
		$snacks = $response['data']['snacks'];
	}
	$custumiseBuyOptionsCheckboxStatus = '';
?>
<section>
    <?php
        if (isset($response['response']['message'])) { ?> 
            <p>
                <?php echo($response['response']['message']); ?>
            </p>
    <?php
            $snacks = $_SESSION['snacks'];
            if ($customiseBuyOptions) {
                $custumiseBuyOptionsCheckboxStatus = 'checked';
            }
        } else if (isset($_POST['command-name']) && $_POST['command-name']=='buy' && $response['response']['status']==200) {
            unset($_SESSION['snacks']);
            header('location: '.BASE_DIR.'index.php?view=buy&command-name=get-to-buy-and-fund-funds');
            exit();
        }
    ?>
    <h1>Fund Moolah: <?php echo($response['data']['fund-funds-amount']) ?> €</h1>
</section>
<section>
    <h1>Shop</h1>
	<form action="<?php echo(BASE_DIR); ?>index.php?view=buy&command-name=get-to-buy-and-fund-funds" method="POST">
		<input type="hidden" name="command-name" value="buy">
		<select name="id" required>
            <?php foreach($snacks as $snack): ?>
                <option value="<?php echo($snack['id']); ?>"<?php if (isset($snackId)&&$snackName==$snack['id']) {echo('selected');} ?>><?php echo($snack['friendly_name']); ?></option>
            <?php endforeach; $_SESSION['snacks'] = $snacks; ?> 
        </select>
		<label>Quantity
			<input type="number" name="quantity" min="1" step="1" max="999" value="<?php if (isset($response['data']['snacks'])) {echo('1');} else {echo($quantity);} ?>" required>
		</label>
		<label>Customise buy options
			<input type="checkbox" name="customise-buy-options" value="yes" <?php echo($custumiseBuyOptionsCheckboxStatus); ?>>
		</label>
			<label>Price
				<input type="number" name="price" min="0.01" step="0.01" max="999.99" value="<?php if (!isset($response['data']['snacks']) && isset($options['price'])) {echo($options['price']);} ?>">
			</label>
			<label>Snacks per box
				<input type="number" name="snacks-per-box" min="1" step="1" max="99" value="<?php if (!isset($response['data']['snacks']) && isset($options['snacks_per_box'])) {echo($options['snacks_per_box']);} ?>">
			</label>
			<label>Expiration in days
				<input type="number" name="expiration-in-days" min="1" step="1" max="9999" value="<?php if (!isset($response['data']['snacks']) && isset($options['expiration_in_days'])) {echo($options['expiration_in_days']);} ?>">
			</label>
		<input type="submit" value="Buy">
	</form>
</section>
<script>
    function askBuyConfirm(event) {
        event.preventDefault();
        var confirmString = 'Buy '+event.target.form[1][event.target.form[1].selectedIndex].innerText+'?';
        console.log(event.target.form);
        if (event.target.form[3].checked) {
            confirmString += '\n\nPrice: '+event.target.form[4].valueAsNumber+' €.\nSnacks per box: '+event.target.form[5].valueAsNumber+'. \nExpiration in days: '+event.target.form[6].valueAsNumber+'.';
        }
        if (confirm(confirmString)) {
            event.target.form.submit();
        }
    }
    document.querySelector('form input[type="submit"]').addEventListener('click', askBuyConfirm);
</script>
