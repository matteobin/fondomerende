<?php 
    require 'process-request.php';
    if (FRIENDLY_URLS) {
        $hrefs = array(BASE_DIR.getTranslatedString('commands', 3), BASE_DIR.getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2), BASE_DIR.getTranslatedString('commands', 4), BASE_DIR.getTranslatedString('commands', 5), BASE_DIR.getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1), BASE_DIR.getTranslatedString('snack', 1), BASE_DIR.getTranslatedString('actions', 1).'/25/1', BASE_DIR.getTranslatedString('login', 1));
    } else {
        $hrefs = array(BASE_DIR.'index.php?view='.getTranslatedString('commands', 3).'&command-name=get-user-funds', BASE_DIR.'index.php?view='.getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2), BASE_DIR.'index.php?view='.getTranslatedString('commands', 4).'&command-name=get-to-buy', BASE_DIR.'index.php?view='.getTranslatedString('commands', 5).'&command-name=get-to-eat-and-user-funds', BASE_DIR.'index.php?view='.getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1).'&command-name=get-user-data', BASE_DIR.'index.php?view='.getTranslatedString('snack', 1).'&command-name=get-snacks-data', BASE_DIR.'index.php?view='.getTranslatedString('actions', 1).'&command-name=get-paginated-actions&limit=25&page=1', BASE_DIR.'index.php?view='.getTranslatedString('login', 1));
    }
?>
</header>
<?php if (isset($response['message'])): ?>
    <p style="clear:left"><?php echo $response['message']; ?></p>
<?php endif; ?>
<h3 class="one-column-row"><?php echoTranslatedString('commons', 1); ?>: <?php echo $response['data']['fund-funds-amount']; ?> €</h3>
<h3 class="one-column-row"><?php echoTranslatedString('commons', 2); ?>: <?php echo $response['data']['user-funds-amount']; ?> €</h3>
<h3 class="one-column-row"><?php echoTranslatedString('main', 3) ?></h3>
<p class="one-column-row"><?php echoTranslatedString('main', 4); echo ' '.$_SESSION['user-friendly-name']; ?>!<br><?php echoTranslatedString('main', 5); ?><br><?php echoTranslatedString('main', 6) ?></p>
<ul class="one-column-row">
    <li><?php echoTranslatedString('main', 7); ?> <a href="<?php echo $hrefs[0]; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 3); ?></strong></a> <?php echoTranslatedString('main', 8); ?></li>
    <li><?php echoTranslatedString('main', 9); ?> <a href="<?php echo $hrefs[1]; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 1); ?></strong></a> <?php echoTranslatedString('main', 10); ?></li>
    <li><?php echoTranslatedString('main', 11); ?> <a href="<?php echo $hrefs[2]; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 4); ?></strong></a> <?php echoTranslatedString('main', '12'); ?></li>
    <li><?php echoTranslatedString('main', 13); ?> <a href="<?php echo $hrefs[3]; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 5); ?></strong></a> <?php echoTranslatedString('main', 14) ?></li>
</ul>
<p class="one-column-row"><?php echoTranslatedString('main', 15); ?></p>
<ul class="one-column-row">
    <li><?php echoTranslatedString('main', 16); ?> <a href="<?php echo $hrefs[4]; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 2); ?> <?php echoStrtoupperTranslatedString('user', 1); ?></strong></a>.</li>
    <li><?php echoTranslatedString('main', 17); ?> <a href="<?php echo $hrefs[5]; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 2); ?> <?php echoStrtoupperTranslatedString('snack', 1); ?></strong></a>.</li>
</ul>
<h3 class="one-column-row"><?php echoTranslatedString('main', 18); ?></h3>
<ol class="one-column-row">
    <?php foreach ($response['data']['actions'] as $action): ?>
        <li><?php echo $action; ?></li>
    <?php endforeach; ?>
</ol>
<a class="one-column-row" href="<?php echo $hrefs[6]; ?>"><?php echoTranslatedString('main', 19); ?></a>
<h3 class="one-column-row"><?php echoTranslatedString('main', 20); ?></h3>
<form class="one-column-row" action="<?php echo $hrefs[7]; ?>" method="post">
    <input type="hidden" name="command-name" value="logout">
    <input type="submit" value="<?php echoTranslatedString('main', 21); ?>">
</form>
<footer class="row" style="margin-top:2em">
    <p style="margin:0"><a href="/credits" style="margin:0">Credits</a></p>
</footer>
