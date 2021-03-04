<?php
    if (isset($_POST['command-name']) && $_POST['command-name']==$commandName && isset($response['status']) && $response['status']==200) {
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
<?php if ($response['status']==404 && !isset($response['message'])): ?>
    <h3 class="one-column-row"><?php echo getStringInLang('withdraw', 2); ?></h3>
    <p class="one-column-row"><?php echo getStringInLang('commons', '6'); ?><a href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getStringInLang('commands', 3); else: echo 'index.php?view='.getStringInLang('commands', 3).'&command-name=get-user-funds'; endif; ?>" title="<?php echo getStringInLang('deposit', 1); ?>"><b><?php echo strtoupper(getStringInLang('commands', 3)); ?></b></a><?php echo getStringInLang('commons', 7) ?></p>
<?php else: ?>
    <h3 class="one-column-row"><?php echo $fundsTypeLabel; ?>: <?php echo number_format($funds, 2, getFormat(1), getFormat(2)); ?> €</h3>
    <form id="deposit-or-withdraw-form" class="row" method="post">
        <input type="hidden" name="command-name" value="<?php echo $commandName ?>">
        <input type="hidden" name="funds-amount" value="<?php echo $funds; ?>">
        <div class="one-column-row">
            <label for="<?php echo $commandName; ?>-amount-input"><?php echo getStringInLang('deposit-or-withdraw', 1); ?></label>
            <input type="number" id="<?php echo $commandName; ?>-amount-input" name="amount" min="0.01" step="0.01" max="<?php echo $maxAmount; ?>" placeholder="<?php echo number_format(5.29, 2, getFormat(1), getFormat(2)); ?>" value="<?php if (isset($amount)) {echo $amount;} ?>" required>
        </div>
        <input class="one-column-last-row" type="submit" value="<?php echo $currentView['title']; ?>">
    </form>
    <script>
        var translatedStrings = [
            "<?php echo getFormat(1); ?>",
            "<?php echo getFormat(2); ?>",
            "<?php echo $currentView['title']; ?>"
        ];
    </script>
    <script src="<?php echo WEB_BASE_DIR; ?>js/format-number-string.min.js" async></script>
    <script src="<?php echo WEB_BASE_DIR; ?>js/deposit-or-withdraw.min.js" defer></script>
<?php endif; if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; ?>
