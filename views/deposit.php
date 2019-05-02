<?php
    require_once('process-request.php');
    if (isset($_POST['command-name']) && $_POST['command-name']=='deposit' && isset($response['response']['status']) && $response['response']['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
?>
    <h2><?php echoTranslatedString('commands', 3); ?></h2>
</header>
<?php if (isset($response['response']['message'])): ?> 
        <p><?php echo($response['response']['message']); ?></p>
<?php endif; ?>
<h3><?php echoTranslatedString('commons', 2); ?>: <?php if (isset($_POST['user-funds-amount'])) {echo($_POST['user-funds-amount']);} else {echo($response['data']['user-funds-amount']);} ?> €</h3>
<form action="<?php echo(BASE_DIR); ?>index.php?view=deposit" method="POST">
    <input type="hidden" name="command-name" value="deposit">
    <input type="hidden" name="user-funds-amount" value="<?php if (isset($_POST['user-funds-amount'])) {echo($_POST['user-funds-amount']);} else {echo($response['data']['user-funds-amount']);} ?>">
    <label for="deposit-amount-input"><?php echoTranslatedString('deposit', 2); ?></label>
    <input type="number" id="deposit-amount-input" name="amount" min="0.01" step="0.01" max="99.99" placeholder="5.29" value="<?php if (isset($_POST['amount'])) {echo($_POST['amount']);} ?>" required>
    <input type="submit" value="<?php echoTranslatedString('commands', 3); ?>">
</form>
<script>
    function askDepositConfirm(event) {
        event.preventDefault();
        if (confirm('<?php echoTranslatedString('commands', 3); ?> '+event.target[2].value+' €?')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askDepositConfirm);
</script>
