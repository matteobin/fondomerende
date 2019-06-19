<?php
    require_once('process-request.php');
    if (isset($response['message'])): ?> 
        <p>
            <?php echo($response['message']); ?>
        </p>
    <?php endif; ?>
    <h2><?php echoUcfirstTranslatedString('snack', 1); ?></h2>
<header>
<?php if (empty($response['data']['snacks'])): ?>
    <h3><?php echoTranslatedString('commons', 5); ?> <?php echoTranslatedString('commands', 2); ?>!</h3>
    <p><?php echoTranslatedString('commons', '6'); ?><a href="<?php echo(BASE_DIR) ?>index.php?view=add-snack"><strong><?php echoStrtoupperTranslatedString('commands', 1); ?></strong></a><?php echoTranslatedString('commons', 7) ?></p>
<?php else: ?>>
    <ul>
    <?php foreach($response['data']['snacks'] as $snack): ?>
    <li><?php echo($snack['friendly-name']); ?></h3> <a href="<?php echo(BASE_DIR); ?>index.php?view=edit-snack&command-name=get-snack-data&name=<?php echo($snack['name']); ?>"><?php echoStrtoupperTranslatedString('commands', '2'); ?></a>
        <ul>
            <li><?php echoUcfirstTranslatedString('snack', 3); ?>: <?php echo($snack['price']); ?> €</li>
            <li><?php echoUcfirstTranslatedString('snack', 4); ?>: <?php echo($snack['snacks-per-box']); ?></li>
            <li><?php echoUcfirstTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?>: <?php echo($snack['expiration-in-days']); ?></li>
        </ul>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
