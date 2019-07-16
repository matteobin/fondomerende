    <h2><?php echoUcfirstTranslatedString('commands', 5); ?></h2>
</header>
<?php require 'process-request.php'; ?>
<?php
    if (isset($response['message'])): ?> 
        <p><?php echo $response['message']; ?></p>
<?php 
    elseif (isset($_POST['command-name']) && $_POST['command-name']=='eat' && $response['status']==200):
        $headerString = 'location: '.BASE_DIR;
        if (!FRIENDLY_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    endif; 
?>
<?php if (isset($response['data']['user-funds-amount'])): ?>
    <h3><?php echoTranslatedString('commons', 2); ?>: <?php echo $response['data']['user-funds-amount']; ?> €</h3>
<?php endif; ?>
<?php if ($response['status']==200):
    if (empty($response['data']['snacks'])): ?>
        <h3><?php echoTranslatedString('commons', 5); ?>!</h3>
        <p><?php echoTranslatedString('commons', '6'); ?><a href="<?php echo BASE_DIR; if (FRIENDLY_URLS): echoTranslatedString('commands', 4); else: echo 'index.php?view='.getTranslatedString('commands', 4).'&command-name=get-to-buy'; endif; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 4); ?></strong></a><?php echoTranslatedString('commons', 7) ?></p>
    <?php else: foreach($response['data']['snacks'] as $snack): ?>
        <form method="post">
            <input type="hidden" name="command-name" value="eat"></label>
            <label><?php echo $snack['friendly-name']; ?></label>
            <ul>
                <li><?php echoUcfirstTranslatedString('eat', 2); ?>: <?php echo $snack['quantity']; ?></li>
                <li><?php echoUcfirstTranslatedString('snack', 3) ?>: <?php echo $snack['price-per-snack']; ?> €</li>
                <li><?php echoTranslatedString('snack', 5) ?>: <time datetime="<?php echo $snack['expiration']; ?>"><?php echo $snack['expiration']; ?></time></li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $snack['id']; ?>">
            <input type="submit" value="<?php echoUcfirstTranslatedString('commands', 5); ?> <?php echo $snack['friendly-name']; ?>" class="submit">
        </form>
        <hr>
    <?php endforeach; ?>
    <script>
        function askEatConfirm(event) {
            event.preventDefault();
            if (confirm('<?php echoUcfirstTranslatedString('commands', 5) ?> '+event.target.childNodes[3].innerText+'?')) {
                event.target.submit();
            }
        }
        var submits = document.querySelectorAll('form');
        var submitsNumber = submits.length;
        for (var index=0; index<submitsNumber; index++) {
            submits[index].addEventListener('submit', askEatConfirm);
        }
    </script>
    <?php endif;
endif; ?>

