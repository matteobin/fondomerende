<?php 
    require_once('process-request.php');
    if (isset($response['response']['message'])) {
        echo($response['response']['message']);
    }
?>
</header>
<h3 style="clear:left"><?php echoUcfirstTranslatedString('main', 1); ?> <?php echoTranslatedString('main', 2); ?>: <?php echo($response['data']['fund-funds-amount']); ?> €</h3>
<h3><?php echoUcfirstTranslatedString('main', 3); ?> <?php echoTranslatedString('main', 2); ?>: <?php echo($response['data']['user-funds-amount']); ?> €</h3>
<h3><?php echoTranslatedString('main', 4) ?></h3>
<p><?php echoTranslatedString('main', 5); echo(' '.$_SESSION['user-friendly-name']); ?>!<br><?php echoTranslatedString('main', 6); ?><br><?php echoTranslatedString('main', 7) ?></p>
<ul>
<li><?php echoTranslatedString('main', 8); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=deposit&command-name=get-user-funds"><strong><?php echoStrtoupperTranslatedString('main', 9); ?></strong></a> <?php echoTranslatedString('main', 10); ?></li>
<li><?php echoTranslatedString('main', 11); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=add-snack"><strong><?php echoStrtoupperTranslatedString('main', 12); ?></strong></a> <?php echoTranslatedString('main', 13); ?></li>
<li><?php echoTranslatedString('main', 14); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=buy&command-name=get-to-buy-and-fund-funds"><strong><?php echoStrtoupperTranslatedString('main', 15); ?></strong></a> <?php echoTranslatedString('main', '16'); ?></li>
<li><?php echoTranslatedString('main', 17); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=eat&command-name=get-to-eat-and-user-funds"><strong><?php echoStrtoupperTranslatedString('main', 18); ?></strong></a> <?php echoTranslatedString('main', 19) ?></li>
</ul>
<p><?php echoTranslatedString('main', 20); ?></p>
<ul>
<li><?php echoTranslatedString('main', 21); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=edit-user&command-name=get-user-data"><strong><?php echoStrtoupperTranslatedString('main', 22); ?> <?php echoStrtoupperTranslatedString('main', 3); ?></strong></a>.</li>
<li><?php echoTranslatedString('main', 23); ?> <a href="<?php echo(BASE_DIR); ?>index.php?view=list-snacks-to-edit&command-name=get-snacks-data"><strong><?php echoStrtoupperTranslatedString('main', 22); ?> <?php echoStrtoupperTranslatedString('main', 24); ?></strong></a>.</li>
</ul>
<h3><?php echoTranslatedString('main', 25); ?></h3>
<ul>
    <?php foreach ($response['data']['actions'] as $action): ?>
        <li><?php echo($action); ?></li>
    <?php endforeach; ?>
</ul>
    <h3><?php echoTranslatedString('main', 26); ?></h3>
<form action="<?php echo(BASE_DIR); ?>index.php?view=login" method="POST">
    <input type="hidden" name="command-name" value="logout">
    <input type="submit" value="<?php echoTranslatedString('main', 27); ?>">
</form>
