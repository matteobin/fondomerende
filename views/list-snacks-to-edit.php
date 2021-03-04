    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php
    require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';
    if (isset($response['message'])): ?> 
        <p class="one-column-row error"><?php echo $response['message']; ?></p>
    <?php endif;
    if ($response['status']==404): ?>
        <h3 class="one-column-row"><?php echo getStringInLang('commons', 5); ?>!</h3>
        <p class="one-column-row"><?php echo getStringInLang('commons', '6'); ?><a href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getStringInLang('commands', 1).'-'.getStringInLang('snack', 2); else: echo 'index.php?view='.getStringInLang('commands', 1).'-'.getStringInLang('snack', 2); endif; ?>"><b><?php echo strtoupper(getStringInLang('commands', 1)); ?></b></a><?php echo getStringInLang('commons', 7) ?></p>
<?php elseif ($response['status']==200): ?>
    <ul class="one-column-row">
        <?php foreach($response['data']['snacks'] as $snack): ?>
        <li><?php echo $snack['friendly-name']; ?> <?php if ($snack['visible']==0): echo ' ('.getStringInLang('snack', 7).')'; endif; ?></h3> <a href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getStringInLang('commands', 2).'-'.getStringInLang('snack', 2).'/'; else: echo 'index.php?view='.getStringInLang('commands', 2).'-'.getStringInLang('snack', 2).'&command-name=get-snack-data&name='; endif; echo $snack['name']; ?>"><?php echo strtoupper(getStringInLang('commands', '2')); ?></a>
            <ul>
                <li><?php echo ucfirst(getStringInLang('snack', 3)); ?>: <?php echo number_format($snack['price'], 2, getFormat(1), getFormat(2)); ?> €</li>
                <li><?php echo ucfirst(getStringInLang('snack', 4)); ?>: <?php echo $snack['snacks-per-box']; ?></li>
                <li><?php echo ucfirst(getStringInLang('snack', 5)); ?> <?php echo getStringInLang('snack', 6); ?>: <?php echo $snack['expiration-in-days']; ?></li>
            </ul>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
