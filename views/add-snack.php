<?php
	if (isset($_POST['name'])) {
		require_once('process-request.php');
	}
	if (isset($response['response']['status']) && $response['response']['status']==201) {
        header('location: '.DIR.'index.php?view=main&command-name=get-main-view-data');
		exit();
	} 
	if (isset($response['response']['message'])): ?> 
	<p>
		<?php echo($response['response']['message']); ?>
	</p>
<?php endif; ?>
<form action="<?php echo(DIR); ?>index.php?view=add-snack" method="POST">
    <input type="hidden" name="command-name" value="add-snack">
    <label for="snack-name-input">Snack name</label>
    <input type="text" name="name" id="snack-name-input" placeholder="name" value="<?php if (isset($name)) {echo($name);} ?>" required>
    <label for="price-input">Price</label>
    <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="999.99" placeholder="0.07" value="<?php if (isset($price)) {echo($price);} ?>" required>
    <label for="snacks-per-box-input">Snacks per box</label>
    <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="99" placeholder="7" value="<?php if (isset($snacksPerBox)) {echo($snacksPerBox);} ?>" required>
    <label for="expiration-in-days-input">Expiration in days</label>
    <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php if (isset($expirationInDays)) {echo($expirationInDays);} ?>" required>
    <input type="submit" value="Add">
</form>