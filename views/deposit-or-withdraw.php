<?php
    if (isset($_POST['command-name']) && $_POST['command-name']==$commandName && isset($response['status']) && $response['status']==200) {
        $headerString = 'location: '.BASE_DIR;
        if (!FRIENDLY_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
?>
    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
        <p class="one-column-row"><?php echo $response['message']; ?></p>
<?php endif; ?>
<h3 class="one-column-row"><?php echo $fundsTypeLabel; ?>: <?php echo $funds; ?> €</h3>
<form class="row" method="post">
    <input type="hidden" name="command-name" value="<?php echo $commandName ?>">
    <input type="hidden" name="funds-amount" value="<?php echo $funds; ?>">
    <div class="one-column-row">
        <label for="<?php echo $commandName; ?>-amount-input"><?php echoTranslatedString('deposit', 2); ?></label>
        <input type="number" id="<?php echo $commandName; ?>-amount-input" name="amount" min="0.01" step="0.01" max="<?php echo $maxAmount; ?>" placeholder="5.29" value="<?php if (isset($amount)) {echo $amount;} ?>" required>
    </div>
    <input class="one-column-last-row" type="submit" value="<?php echo $currentView['title']; ?>">
</form>
<?php require '../echoLibreJS.php'; ?>
<script>
    function askDepositOrWithdrawConfirm(event) {
        event.preventDefault();
        if (confirm('<?php echo $currentView['title']; ?> '+event.target[2].value+' €?')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askDepositOrWithdrawConfirm);
</script>
