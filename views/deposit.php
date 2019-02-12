<?php require_once('process-request.php'); ?>
<section>
<?php
	if (isset($response['response']['message'])): ?> 
		<p>
			<?php echo($response['response']['message']); ?>
		</p>
<?php 
    elseif (isset($_POST['command-name']) && $_POST['command-name']=='deposit' && $response['response']['status']==200):
		header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
		exit();
	endif;
?>
<h1>Moolah: <?php if (isset($response['data']['user-funds-amount'])) {echo($response['data']['user-funds-amount']);} else {echo($userFundsAmount);} ?> €</h1>
</section>
<section>
<h1>Deposit</h1>
	<form action="<?php echo(BASE_DIR); ?>index.php?view=deposit" method="POST">
		<input type="hidden" name="command-name" value="deposit">
		<input type="hidden" name="user-funds-amount" value="<?php if (isset($response['data']['user-funds-amount'])) {echo($response['data']['user-funds-amount']);} else {echo($userFundsAmount);} ?>">
		<label>Amount</label>
		<input type="number" name="amount" min="0.01" step="0.01" max="99.99" value="<?php if (isset($amount)) {echo($amount);} ?>">
		<input type="submit" value="Deposit">
	</form>
</section>
