    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php
    require 'process-request.php';
    if (isset($response['message'])): ?> 
        <p class="one-column-row"><?php echo $response['message']; ?></p>
    <?php endif;
    if ($response['status']==404): ?>
        <h3 class="one-column-row"><?php echoTranslatedString('commons', 5); ?>!</h3>
        <p class="one-column-row"><?php echoTranslatedString('commons', '6'); ?><a href="<?php echo BASE_DIR; if (FRIENDLY_URLS): echo getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2); else: echo 'index.php?view='.getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2); endif; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 1); ?></strong></a><?php echoTranslatedString('commons', 7) ?></p>
<?php elseif ($response['status']==200): ?>
    <ul class="one-column-row">
        <?php foreach($response['data']['snacks'] as $snack): ?>
        <li><?php echo $snack['friendly-name']; ?> <?php if ($snack['visible']==0): echo ' ('.getTranslatedString('snack', 7).')'; endif; ?></h3> <a href="<?php echo BASE_DIR; if (FRIENDLY_URLS): echo getTranslatedString('commands', 2).'-'.getTranslatedString('snack', 2).'/'; else: echo 'index.php?view='.getTranslatedString('commands', 2).'-'.getTranslatedString('snack', 2).'&command-name=get-snack-data&name='; endif; echo $snack['name']; ?>"><?php echoStrtoupperTranslatedString('commands', '2'); ?></a>
            <ul>
                <li><?php echoUcfirstTranslatedString('snack', 3); ?>: <?php echo number_format($snack['price'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)); ?> €</li>
                <li><?php echoUcfirstTranslatedString('snack', 4); ?>: <?php echo $snack['snacks-per-box']; ?></li>
                <li><?php echoUcfirstTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?>: <?php echo $snack['expiration-in-days']; ?></li>
            </ul>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
