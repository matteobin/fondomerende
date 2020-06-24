<?php 
    require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php'; 
    if (isset($_POST['command-name']) && $_POST['command-name']=='eat' && $response['status']==200) {
        $headerString = 'Location: '.WEB_BASE_DIR;
        if (!CLEAN_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
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
    <h3 class="one-column-row"><?php echo getTranslatedString('commons', 2); ?>: <?php echo number_format($response['data']['user-funds-amount'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)); ?> €</h3>
<?php endif; if ($response['status']==404 && $commandName=='get-to-eat-and-user-funds'): ?>
    <h3 class="one-column-row"><?php echo getTranslatedString('commons', 5); ?>!</h3>
    <p class="one-column-row"><?php echo getTranslatedString('commons', '6'); ?><a href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getTranslatedString('commands', 5); else: echo 'index.php?view='.getTranslatedString('commands', 5).'&command-name=get-to-buy'; endif; ?>" title="<?php echo getTranslatedString('buy', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 5)); ?></b></a><?php echo getTranslatedString('commons', 7) ?></p>
    <?php elseif ($response['status']==200): foreach($response['data']['snacks'] as $snack): ?>
        <form class="row" method="post">
            <input type="hidden" name="command-name" value="eat"></label>
            <label class="one-column-row"><?php echo $snack['friendly-name']; ?></label>
            <ul class="one-column-row">
                <li><?php echo ucfirst(getTranslatedString('eat', 2)); ?>: <?php echo $snack['quantity']; ?></li>
                <li><?php echo ucfirst(getTranslatedString('snack', 3)); ?>: <?php echo number_format($snack['price-per-snack'], 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)); ?> €</li>
                <li><?php echo getTranslatedString('snack', 5) ?>: <time datetime="<?php echo $snack['expiration']; ?>"><?php echo $snack['expiration']; ?></time></li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $snack['id']; ?>">
            <input class="one-column-row" type="submit" value="<?php echo ucfirst(getTranslatedString('commands', 6)); ?> <?php echo $snack['friendly-name']; ?>">
        </form>
        <hr class="one-column-row" style="width:100%">
    <?php endforeach; echoResource('librejs-html'); ?>
    <script>
        function askEatConfirm(event) {
            event.preventDefault();
            if (confirm('<?php echo ucfirst(getTranslatedString('commands', 6)); ?> '+event.target.childNodes[3].innerText+'?')) {
                event.target.submit();
            }
        }
        var submits = document.getElementsByTagName('form');
        var submitsNumber = submits.length;
        for (var index=0; index<submitsNumber; index++) {
            submits[index].addEventListener('submit', askEatConfirm);
        }
    </script>
<?php endif; ?>
