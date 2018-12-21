<?php require_once('process-request.php'); ?>
<section>
<?php
	if (isset($response['response']['message'])): ?> 
		<p>
			<?php echo($response['response']['message']); ?>
		</p>
<?php 
    elseif (isset($_POST['command-name']) && $_POST['command-name']=='deposit' && $response['response']['status']==200):
		header('location: index.php?view=deposit&command-name=get-user-funds');
		exit();
	endif;
?>
<h1>Moolah: <?php if (isset($response['data']['user-funds-amount'])) {echo($response['data']['user-funds-amount']);} else {echo($userFundsAmount);} ?> €</h1>
</section>
<section>
<h1>Deposit</h1>
	<form action="index.php?view=deposit" method="POST">
		<input type="hidden" name="command-name" value="deposit">
		<input type="hidden" name="user-funds-amount" value="<?php if (isset($response['data']['user-funds-amount'])) {echo($response['data']['user-funds-amount']);} else {echo($userFundsAmount);} ?>">
		<label>Amount</label>
		<input type="number" name="amount" value="<?php if (isset($amount)) {echo($amount);} ?>">
		<input type="submit" value="Deposit">
	</form>
</section>