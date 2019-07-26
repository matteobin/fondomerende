<?php
    require 'process-request.php';
    if (isset($_POST['command-name']) && $_POST['command-name']=='deposit' && isset($response['status']) && $response['status']==200) {
        $headerString = 'location: '.BASE_DIR;
        if (!FRIENDLY_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
?>
    <h2><?php echoUcfirstTranslatedString('commands', 3); ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
        <p><?php echo $response['message']; ?></p>
<?php endif; ?>
<h3><?php echoTranslatedString('commons', 2); ?>: <?php if (isset($_POST['user-funds-amount'])) {echo $_POST['user-funds-amount'];} else {echo $response['data']['user-funds-amount'];} ?> €</h3>
<form method="post">
    <input type="hidden" name="command-name" value="deposit">
    <input type="hidden" name="user-funds-amount" value="<?php if (isset($_POST['user-funds-amount'])) {echo $_POST['user-funds-amount'];} else {echo $response['data']['user-funds-amount'];} ?>">
    <div style="margin-bottom:1em">
        <label for="deposit-amount-input"><?php echoTranslatedString('deposit', 2); ?></label>
        <input type="number" id="deposit-amount-input" name="amount" min="0.01" step="0.01" max="99.99" placeholder="5.29" value="<?php if (isset($_POST['amount'])) {echo $_POST['amount'];} ?>" required>
    </div>
    <div style="clear:left">
        <input type="submit" value="<?php echoUcfirstTranslatedString('commands', 3); ?>">
    </div>
</form>
<script>
    function askDepositConfirm(event) {
        event.preventDefault();
        if (confirm('<?php echoUcfirstTranslatedString('commands', 3); ?> '+event.target[2].value+' €?')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askDepositConfirm);
</script>
