<?php 
    require_once('process-request.php');
    if (isset($response['response']['message'])) {
        echo($response['response']['message']);
    }
?>
</header>
<h3 style="clear:left"><?php echoTranslatedString('main', 1);?>: <?php echo($response['data']['fund-funds-amount']); ?> €</h3>
<h3><?php echoTranslatedString('main', 2); ?>: <?php echo($response['data']['user-funds-amount']); ?> €</h3>
<h3><?php echoTranslatedString('main', 3) ?></h3>
<p><?php echoTranslatedString('main', 4); echo($_SESSION['user-friendly-name']); ?>!<br><?php echoTranslatedString('main', 5); ?><br><?php echoTranslatedString('main', 6) ?></p>
<ul>
<li><?php echoTranslatedString('main', 7); ?><a href="<?php echo(BASE_DIR); ?>index.php?view=deposit&command-name=get-user-funds"><strong><?php echoTranslatedString('main', 8); ?></strong></a><?php echoTranslatedString('main', 9); ?></li>
<li><?php echoTranslatedString('main', 10); ?><a href="<?php echo(BASE_DIR); ?>index.php?view=add-snack"><strong><?php echoTranslatedString('main', 11); ?></strong></a><?php echoTranslatedString('main', 12); ?></li>
    <li>Move your lazy butt, get out to <a href="<?php echo(BASE_DIR); ?>index.php?view=buy&command-name=get-to-buy-and-fund-funds"><strong>BUY</strong></a> the damn snacks.</li>
    <li>Done? Now chill: open the fridge and <a href="<?php echo(BASE_DIR); ?>index.php?view=eat&command-name=get-to-eat-and-user-funds"><strong>EAT</strong></a> them all.</li>
</ul>
<p>You don't like the world you live in? Maybe it's time to start doing something about it:</p>
<ul>
    <li>Change what you see in the mirror: <a href="<?php echo(BASE_DIR); ?>index.php?view=edit-user&command-name=get-user-data"><strong>EDIT USER</strong></a>.</li>
    <li>Modify your full fat diet: <a href="<?php echo(BASE_DIR); ?>index.php?view=list-snacks-to-edit&command-name=get-snacks-data"><strong>EDIT SNACKS</strong></a>.</li>
</ul>
<h3>Neighbourhood happenings:</h3>
<ul>
    <?php foreach ($response['data']['actions'] as $action): ?>
        <li><?php echo($action); ?></li>
    <?php endforeach; ?>
</ul>
<h3>Tired? Log the hell out of here, slut.</h3>
<form action="<?php echo(BASE_DIR); ?>index.php?view=login" method="POST">
    <input type="hidden" name="command-name" value="logout">
    <input type="submit" value="See ya">
</form>
