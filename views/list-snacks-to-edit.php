<?php
    require_once('process-request.php');
    if (isset($response['response']['message'])): ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
    <?php endif; ?>
    <h2><?php echoUcfirstTranslatedString('main', 24); ?></h2>
<header>
<ul>
<?php foreach($response['data']['snacks'] as $snack): ?>
<li><?php echo($snack['friendly-name']); ?></h3> <a href="<?php echo(BASE_DIR); ?>index.php?view=edit-snack&command-name=get-snack-data&name=<?php echo($snack['name']); ?>"><?php echoStrtoupperTranslatedString('main', '22'); ?></a>
    <ul>
        <li><?php echoUcfirstTranslatedString('add-snack', 3); ?>: <?php echo($snack['price']); ?> €</li>
        <li><?php echoUcfirstTranslatedString('add-snack', 4); ?>: <?php echo($snack['snacks-per-box']); ?></li>
        <li><?php echoUcfirstTranslatedString('add-snack', 5); ?>: <?php echo($snack['expiration-in-days']); ?></li>
    </ul>
<?php endforeach; ?>
</ul>
