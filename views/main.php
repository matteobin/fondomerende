<?php 
    require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';
    if (CLEAN_URLS) {
        $hrefs = array(WEB_BASE_DIR.getTranslatedString('commands', 3), WEB_BASE_DIR.getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2), WEB_BASE_DIR.getTranslatedString('commands', 5), WEB_BASE_DIR.getTranslatedString('commands', 6), WEB_BASE_DIR.getTranslatedString('commands', 4), WEB_BASE_DIR.getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1), WEB_BASE_DIR.getTranslatedString('snack', 1), WEB_BASE_DIR.getTranslatedString('actions', 1).'/25/1', WEB_BASE_DIR.getTranslatedString('login', 1), WEB_BASE_DIR.getTranslatedString('credits', 1));
    } else {
        $hrefs = array(WEB_BASE_DIR.'index.php?view='.getTranslatedString('commands', 3).'&command-name=get-user-funds', WEB_BASE_DIR.'index.php?view='.getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2), WEB_BASE_DIR.'index.php?view='.getTranslatedString('commands', 5).'&command-name=get-to-buy', WEB_BASE_DIR.'index.php?view='.getTranslatedString('commands', 6).'&command-name=get-to-eat-and-user-funds', WEB_BASE_DIR.'index.php?view='.getTranslatedString('commands', 4).'&command-name=get-fund-funds', WEB_BASE_DIR.'index.php?view='.getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1).'&command-name=get-user-data', WEB_BASE_DIR.'index.php?view='.getTranslatedString('snack', 1).'&command-name=get-snacks-data', WEB_BASE_DIR.'index.php?view='.getTranslatedString('actions', 1).'&command-name=get-paginated-actions&limit=25&page=1', WEB_BASE_DIR.'index.php?view='.getTranslatedString('login', 1), WEB_BASE_DIR.'index.php?view='.getTranslatedString('credits', 1));
    }
?>
</header>
<?php if (isset($response['message'])): ?>
    <p class="one-column-row error" style="clear:left"><?php echo $response['message']; ?></p>
<?php endif; ?>
<h3 class="one-column-row"><?php echo getTranslatedString('commons', 1); ?>: <?php echo number_format($response['data']['fund-funds-amount'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)); ?> €</h3>
<h3 class="one-column-row"><?php echo getTranslatedString('commons', 2); ?>: <?php echo number_format($response['data']['user-funds-amount'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 1)); ?> €</h3>
<h3 class="one-column-row"><?php echo getTranslatedString('main', 3); ?></h3>
<p class="one-column-row"><?php echo getTranslatedString('main', 4); echo ' '.$_SESSION['user-friendly-name']; ?>!<br><?php echo getTranslatedString('main', 5); ?><br><?php echo getTranslatedString('main', 6); ?></p>
<ul class="one-column-row">
    <li><?php echo getTranslatedString('main', 7); ?> <a href="<?php echo $hrefs[0]; ?>" title="<?php echo getTranslatedString('deposit', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 3)); ?></b></a> <?php echo getTranslatedString('main', 8); ?></li>
    <li><?php echo getTranslatedString('main', 9); ?> <a href="<?php echo $hrefs[1]; ?>" title="<?php echo getTranslatedString('add-snack', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 1)); ?></b></a> <?php echo getTranslatedString('main', 10); ?></li>
    <li><?php echo getTranslatedString('main', 11); ?> <a href="<?php echo $hrefs[2]; ?>" title="<?php echo getTranslatedString('buy', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 5)); ?></b></a> <?php echo getTranslatedString('main', '12'); ?></li>
    <li><?php echo getTranslatedString('main', 13); ?> <a href="<?php echo $hrefs[3]; ?>" title="<?php echo getTranslatedString('eat', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 6)); ?></b></a> <?php echo getTranslatedString('main', 14) ?></li>
    <li><a href="<?php echo $hrefs[4]; ?>" title="<?php echo getTranslatedString('withdraw', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 4)); ?></b></a> <?php echo getTranslatedString('main', 15); ?> <b><?php echo getTranslatedString('main', 16); ?></b></li>
</ul>
<p class="one-column-row"><?php echo getTranslatedString('main', 17); ?></p>
<ul class="one-column-row">
    <li><?php echo getTranslatedString('main', 18); ?> <a href="<?php echo $hrefs[5]; ?>" title="<?php echo getTranslatedString('edit-user', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 2)); ?> <?php echo strtoupper(getTranslatedString('user', 1)); ?></b></a>.</li>
    <li><?php echo getTranslatedString('main', 19); ?> <a href="<?php echo $hrefs[6]; ?>" title="<?php echo getTranslatedString('edit-snack', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 2)); ?> <?php echo strtoupper(getTranslatedString('snack', 1)); ?></b></a>.</li>
</ul>
<h3 class="one-column-row"><?php echo getTranslatedString('main', 20); ?></h3>
<ol class="one-column-row">
    <?php foreach ($response['data']['actions'] as $action): ?>
        <li><?php echo $action; ?></li>
    <?php endforeach; ?>
</ol>
<a class="one-column-row" href="<?php echo $hrefs[7]; ?>" title="<?php echo getTranslatedString('actions', 2); ?>"><?php echo getTranslatedString('main', 21); ?></a>
<h3 class="one-column-row"><?php echo getTranslatedString('main', 22); ?></h3>
<form class="one-column-row" action="<?php echo $hrefs[8]; ?>" method="post">
    <input type="hidden" name="command-name" value="logout">
    <input type="submit" value="<?php echo getTranslatedString('main', 23); ?>">
</form>
