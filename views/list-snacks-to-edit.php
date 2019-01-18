<?php
    require_once('process-request.php');
    if (isset($response['response']['message'])): ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
    <?php endif; ?>
<article>
    <h1>Snacks</h1>
    <ul>
    <?php foreach($response['data']['snacks'] as $snack): ?>
       <li><?php echo($snack['friendly-name']); ?> <a href="index.php?view=edit-snack&command-name=get-snack-data&name=<?php echo($snack['name']); ?>">EDIT</a></li>
        <ul>
            <li>Price: <?php echo($snack['price']); ?> €</li>
            <li>Snacks per box: <?php echo($snack['snacks-per-box']); ?></li>
            <li>Expiration in days: <?php echo($snack['expiration-in-days']); ?></li>
        </ul>
    <?php endforeach; ?>
    </ul>
</article>