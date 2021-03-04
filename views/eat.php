<?php 
    require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php'; 
    if (isset($_POST['command-name']) && $_POST['command-name']=='eat' && $response['status']==200) {
        $headerString = 'Location: '.WEB_BASE_DIR;
        if (!CLEAN_URLS) {
            $headerString .= 'index.php?view='.getStringInLang('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
?>
    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; if (isset($response['data']['user-funds-amount'])): ?>
    <h3 class="one-column-row"><?php echo getStringInLang('commons', 2); ?>: <?php echo number_format($response['data']['user-funds-amount'], 2, getFormat(1), getFormat(2)); ?> €</h3>
<?php endif; if ($response['status']==404 && $commandName=='get-to-eat-and-user-funds'): ?>
    <h3 class="one-column-row"><?php echo getStringInLang('commons', 5); ?>!</h3>
    <p class="one-column-row"><?php echo getStringInLang('commons', '6'); ?><a href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getStringInLang('commands', 5); else: echo 'index.php?view='.getStringInLang('commands', 5).'&command-name=get-to-buy'; endif; ?>" title="<?php echo getStringInLang('buy', 1); ?>"><b><?php echo strtoupper(getStringInLang('commands', 5)); ?></b></a><?php echo getStringInLang('commons', 7) ?></p>
    <?php elseif ($response['status']==200): foreach($response['data']['snacks'] as $snack): ?>
        <form class="row" method="post">
            <input type="hidden" name="command-name" value="eat"></label>
            <label class="one-column-row"><?php echo $snack['friendly-name']; ?></label>
            <ul class="one-column-row">
                <li><?php echo ucfirst(getStringInLang('eat', 2)); ?>: <?php echo $snack['quantity']; ?></li>
                <li><?php echo ucfirst(getStringInLang('snack', 3)); ?>: <?php echo number_format($snack['price-per-snack'], 2, getFormat(1), getFormat(2)); ?> €</li>
                <li><?php echo getStringInLang('snack', 5) ?>: <time datetime="<?php echo $snack['expiration']; ?>"><?php echo $snack['expiration']; ?></time></li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $snack['id']; ?>">
            <input class="one-column-row" type="submit" value="<?php echo ucfirst(getStringInLang('commands', 6)); ?> <?php echo $snack['friendly-name']; ?>">
        </form>
        <hr class="one-column-row" style="width:100%">
    <?php endforeach; ?>
    <script>
        var translatedStrings = ["<?php echo ucfirst(getStringInLang('commands', 6)); ?>"];
    </script>
    <script src="<?php echo WEB_BASE_DIR; ?>js/eat.min.js" defer></script>
<?php endif; ?>
