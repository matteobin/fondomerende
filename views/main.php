<?php 
    require_once('process-request.php');
    if (isset($response['response']['message'])) {
        echo($response['response']['message']);
    }
?>
</header>
<h3 style="clear:left"><?php echoTranslatedString('commons', 1); ?> <?php echoTranslatedString('commons', 2); ?>: <?php echo($response['data']['fund-funds-amount']); ?> €</h3>
<h3><?php echoUcfirstTranslatedString('user', 1); ?> <?php echoTranslatedString('commons', 2); ?>: <?php echo($response['data']['user-funds-amount']); ?> €</h3>
<h3><?php echoTranslatedString('main', 3) ?></h3>
<p><?php echoTranslatedString('main', 4); echo(' '.$_SESSION['user-friendly-name']); ?>!<br><?php echoTranslatedString('main', 5); ?><br><?php echoTranslatedString('main', 6) ?></p>
<ul>
<li><?php echoTranslatedString('main', 7); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=deposit&command-name=get-user-funds"><strong><?php echoStrtoupperTranslatedString('commands', 3); ?></strong></a> <?php echoTranslatedString('main', 8); ?></li>
<li><?php echoTranslatedString('main', 9); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=add-snack"><strong><?php echoStrtoupperTranslatedString('commands', 1); ?></strong></a> <?php echoTranslatedString('main', 10); ?></li>
<li><?php echoTranslatedString('main', 11); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=buy&command-name=get-to-buy-and-fund-funds"><strong><?php echoStrtoupperTranslatedString('commands', 4); ?></strong></a> <?php echoTranslatedString('main', '12'); ?></li>
<li><?php echoTranslatedString('main', 13); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=eat&command-name=get-to-eat-and-user-funds"><strong><?php echoStrtoupperTranslatedString('commands', 5); ?></strong></a> <?php echoTranslatedString('main', 14) ?></li>
</ul>
<p><?php echoTranslatedString('main', 15); ?></p>
<ul>
<li><?php echoTranslatedString('main', 16); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=edit-user&command-name=get-user-data"><strong><?php echoStrtoupperTranslatedString('commands', 2); ?> <?php echoStrtoupperTranslatedString('user', 1); ?></strong></a>.</li>
<li><?php echoTranslatedString('main', 17); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=list-snacks-to-edit&command-name=get-snacks-data"><strong><?php echoStrtoupperTranslatedString('commands', 2); ?> <?php echoStrtoupperTranslatedString('snack', 1); ?></strong></a>.</li>
</ul>
<h3><?php echoTranslatedString('main', 18); ?></h3>
<ul>
    <?php foreach ($response['data']['actions'] as $action): ?>
        <li><?php echo($action); ?></li>
    <?php endforeach; ?>
</ul>
    <h3><?php echoTranslatedString('main', 19); ?></h3>
<form action="<?php echo(BASE_DIR); ?>index.php?view=login" method="POST">
    <input type="hidden" name="command-name" value="logout">
    <input type="submit" value="<?php echoTranslatedString('main', 20); ?>">
</form>
