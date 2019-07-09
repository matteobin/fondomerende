<?php
    require 'process-request.php';    
    if (isset($_POST['command-name']) && $_POST['command-name']=='edit-snack' && isset($response['status']) && $response['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
?>
    <h2><?php echoUcfirstTranslatedString('commands', 2); ?> <?php echoTranslatedString('snack', 2); ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
    <p><?php echo $response['message']; ?></p>
<?php endif; ?>
<form method="post">
    <input type="hidden" name="command-name" value="edit-snack">
    <input type="hidden" name="id" value="<?php if (isset($_POST['id'])) {echo $_POST['id'];} else {echo $response['data']['snack']['id'];} ?>">
    <label for="snack-name-input"><?php echoUcfirstTranslatedString('commons', 3); ?></label>
    <input type="text" name="name" id="snack-name-input" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} else {echo $response['data']['snack']['friendly-name'];} ?>" required>
    <label for="price-input"><?php echoUcfirstTranslatedString('snack', 3); ?></label>
    <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="99.99" placeholder="0.07" value="<?php if (isset($_POST['price'])) {echo $_POST['price'];} else {echo $response['data']['snack']['price'];} ?>" required>
    <label for="snacks-per-box-input"><?php echoUcfirstTranslatedString('snack', 4); ?></label>
    <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="999" placeholder="7" value="<?php if (isset($_POST['snacks-per-box'])) {echo $_POST['snacks-per-box'];} else {echo $response['data']['snack']['snacks-per-box'];} ?>" required>
    <label for="expiration-in-days-input"><?php echoTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?></label>
    <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php if (isset($_POST['expiration-in-days'])) {echo $_POST['expiration-in-days'];} else {echo $response['data']['snack']['expiration-in-days'];} ?>" required>
    <label for="visible-input">Hidden</label>
    <input type="hidden" name="visible" value="yes">
    <input type="checkbox" name="visible" id="visible-input" value="no" <?php if (isset($_POST['visible']) && $_POST['visible']=='no') {echo 'checked';} else if (isset($response['data']['snack']['visible']) && $response['data']['snack']['visible']==0) {echo 'checked';} ?>>
    <input type="submit" value="<?php echoTranslatedString('commons', 4); ?>">
</form>
<script>
    function askEditSnackConfirm(event) {
        event.preventDefault();
        var confirmString = 'Edit snack '+event.target[2].value+'?\n\nPrice: '+event.target[3].value+' €.\nSnacks per box: '+event.target[4].value+'.\nExpiration in days: '+event.target[5].value+'.';
        if (event.target[6].checked) {
            confirmString += '\nHidden.';
        } else {
            confirmString += '\nVisible.';
        }
        if (confirm(confirmString)) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askEditSnackConfirm);
</script>
