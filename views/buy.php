<?php require_once('process-request.php'); ?>
<section>
<?php
	if (isset($response['response']['message'])): ?> 
		<p>
			<?php echo($response['response']['message']); ?>
		</p>
<?php 
    elseif (isset($_POST['command-name']) && $_POST['command-name']=='buy'):
		header('location: index.php?view=deposit&command-name=get-to-buy');
		exit();
	endif;
?>
<h1>Shop</h1>
	<form action="index.php?view=buy" method="POST">
		<input type="hidden" name="command-name" value="buy">
		<select name="snack-name">

            <?php foreach($response['data']['snacks'] as $snack): ?>
                <option value="<?php echo($snack['name']); ?>"><?php echo($snack['friendly_name']); ?></option>
            <?php endforeach; ?> 
        </select>
        <input type="number" name="quantity" min="1" value="1">
		<input type="submit" value="Buy">
	</form>
</section>