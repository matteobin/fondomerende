<?php require_once('process-request.php'); ?>
<section>
<?php
	if (isset($_POST['command-name']) && $_POST['command-name']=='eat') {
		header('location: index.php?view=eat&command-name=get-to-eat-and-user-funds');
		exit();
	}
	if (isset($response['response']['message'])): ?> 
		<p>
			<?php echo($response['response']['message']); ?>
		</p>
<?php endif; ?>
<h1>Moolah: <?php echo($response['data']['user-funds-amount']) ?> €	</h1>
</section>
<section>
<h1>Pantry</h1>
<?php foreach($response['data']['snacks'] as $snack): ?>
	<form action="index.php?view=eat&command-name=get-eatable-and-funds" method="POST">
		<input type="hidden" name="command-name" value="eat">
		<label><?php echo($snack['friendly-name']) ?></label>
		<ul>
			<li>Available: <?php echo($snack['quantity']) ?></li>
			<li>Price: <?php echo($snack['price-per-snack']) ?> €</li>
		</ul>
		<input type="hidden" name="snack-name" value="<?php echo($snack['name']) ?>">
		<input type="submit" value="Eat <?php echo($snack['friendly-name']) ?>">
	</form>
	<?php endforeach; ?>
</section>