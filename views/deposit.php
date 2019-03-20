<?php
    require_once('process-request.php');
    if (isset($_POST['command-name']) && $_POST['command-name']=='deposit' && isset($response['response']['status']) && $response['response']['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
?>
    <h2>Deposit</h2>
</header>
<?php if (isset($response['response']['message'])): ?> 
        <p><?php echo($response['response']['message']); ?></p>
<?php endif; ?>
<h3>Moolah: <?php if (isset($_POST['user-funds-amount'])) {echo($_POST['user-funds-amount']);} else {echo($response['data']['user-funds-amount']);} ?> €</h3>
<form action="<?php echo(BASE_DIR); ?>index.php?view=deposit" method="POST">
    <input type="hidden" name="command-name" value="deposit">
    <input type="hidden" name="user-funds-amount" value="<?php if (isset($_POST['user-funds-amount'])) {echo($_POST['user-funds-amount']);} else {echo($response['data']['user-funds-amount']);} ?>">
    <label>Amount</label>
    <input type="number" name="amount" min="0.01" step="0.01" max="99.99" placeholder="5.29" value="<?php if (isset($_POST['amount'])) {echo($_POST['amount']);} ?>" required>
    <input type="submit" value="Deposit">
</form>
<script>
    function askDepositConfirm(event) {
        event.preventDefault();
        if (confirm('Deposit '+event.target[2].value+' €?')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askDepositConfirm);
</script>
