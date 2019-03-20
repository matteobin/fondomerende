<?php
    require_once('process-request.php');
    if (isset($response['response']['message'])): ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
    <?php endif; ?>
    <h2>Snacks</h2>
<header>
<ul>
<?php foreach($response['data']['snacks'] as $snack): ?>
   <li><?php echo($snack['friendly-name']); ?></h3> <a href="<?php echo(BASE_DIR); ?>index.php?view=edit-snack&command-name=get-snack-data&name=<?php echo($snack['name']); ?>">EDIT</a>
    <ul>
        <li>Price: <?php echo($snack['price']); ?> €</li>
        <li>Snacks per box: <?php echo($snack['snacks-per-box']); ?></li>
        <li>Expiration in days: <?php echo($snack['expiration-in-days']); ?></li>
    </ul>
<?php endforeach; ?>
</ul>
