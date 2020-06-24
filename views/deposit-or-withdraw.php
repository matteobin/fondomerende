<?php
    if (isset($_POST['command-name']) && $_POST['command-name']==$commandName && isset($response['status']) && $response['status']==200) {
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
<?php if ($response['status']==404 && !isset($response['message'])): ?>
    <h3 class="one-column-row"><?php echo getTranslatedString('withdraw', 2); ?></h3>
    <p class="one-column-row"><?php echo getTranslatedString('commons', '6'); ?><a href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getTranslatedString('commands', 3); else: echo 'index.php?view='.getTranslatedString('commands', 3).'&command-name=get-user-funds'; endif; ?>" title="<?php echo getTranslatedString('deposit', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 3)); ?></b></a><?php echo getTranslatedString('commons', 7) ?></p>
<?php else: ?>
    <h3 class="one-column-row"><?php echo $fundsTypeLabel; ?>: <?php echo number_format($funds, 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)); ?> €</h3>
    <form class="row" method="post">
        <input type="hidden" name="command-name" value="<?php echo $commandName ?>">
        <input type="hidden" name="funds-amount" value="<?php echo $funds; ?>">
        <div class="one-column-row">
            <label for="<?php echo $commandName; ?>-amount-input"><?php echo getTranslatedString('deposit-or-withdraw', 1); ?></label>
            <input type="number" id="<?php echo $commandName; ?>-amount-input" name="amount" min="0.01" step="0.01" max="<?php echo $maxAmount; ?>" placeholder="<?php echo number_format(5.29, 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)); ?>" value="<?php if (isset($amount)) {echo $amount;} ?>" required>
        </div>
        <input class="one-column-last-row" type="submit" value="<?php echo $currentView['title']; ?>">
    </form>
    <?php echoResource('librejs-html'); ?>
    <script>
        var decimalPointSeparator = '<?php echo getTranslatedString('number-separators', 1); ?>';
        var thousandsSeparator = '<?php echo getTranslatedString('number-separators', 2); ?>';
        <?php echoResource('format-number-string-js'); ?>
        function askDepositOrWithdrawConfirm(event) {
            event.preventDefault();
            if (confirm('<?php echo $currentView['title']; ?> '+formatNumberString(event.target[2].value)+' €?')) {
                event.target.submit();
            }
        }
        document.querySelector('form').addEventListener('submit', askDepositOrWithdrawConfirm);
    </script>
<?php endif; if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; ?>
