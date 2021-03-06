    <h2 class="one-column-row"><?php echoUcfirstTranslatedString('commands', 5); ?></h2>
</header>
<?php require 'process-request.php'; ?>
<?php
    if (isset($response['message'])): ?> 
        <p class="one-column-row"><?php echo $response['message']; ?></p>
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
    <h3 class="one-column-row"><?php echoTranslatedString('commons', 2); ?>: <?php echo $response['data']['user-funds-amount']; ?> €</h3>
<?php endif; ?>
<?php if ($response['status']==404): ?>
    <h3 class="one-column-row"><?php echoTranslatedString('commons', 5); ?>!</h3>
    <p class="one-column-row"><?php echoTranslatedString('commons', '6'); ?><a href="<?php echo BASE_DIR; if (FRIENDLY_URLS): echoTranslatedString('commands', 4); else: echo 'index.php?view='.getTranslatedString('commands', 4).'&command-name=get-to-buy'; endif; ?>"><strong><?php echoStrtoupperTranslatedString('commands', 4); ?></strong></a><?php echoTranslatedString('commons', 7) ?></p>
    <?php elseif ($response['status']==200): foreach($response['data']['snacks'] as $snack): ?>
        <form class="row" method="post">
            <input type="hidden" name="command-name" value="eat"></label>
            <label class="one-column-row"><?php echo $snack['friendly-name']; ?></label>
            <ul class="one-column-row">
                <li><?php echoUcfirstTranslatedString('eat', 2); ?>: <?php echo $snack['quantity']; ?></li>
                <li><?php echoUcfirstTranslatedString('snack', 3) ?>: <?php echo $snack['price-per-snack']; ?> €</li>
                <li><?php echoTranslatedString('snack', 5) ?>: <time datetime="<?php echo $snack['expiration']; ?>"><?php echo $snack['expiration']; ?></time></li>
            </ul>
            <input type="hidden" name="id" value="<?php echo $snack['id']; ?>">
            <input class="one-column-row" type="submit" value="<?php echoUcfirstTranslatedString('commands', 5); ?> <?php echo $snack['friendly-name']; ?>">
        </form>
        <hr class="one-column-row" style="width:100%">
    <?php endforeach; require '../echoLibreJS.php'; ?>
    <script>
        function askEatConfirm(event) {
            event.preventDefault();
            if (confirm('<?php echoUcfirstTranslatedString('commands', 5) ?> '+event.target.childNodes[3].innerText+'?')) {
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
