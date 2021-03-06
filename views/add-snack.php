<?php
    if (isset($_POST['name'])) {
        require 'process-request.php';
    }
    if (isset($response['status']) && $response['status']==201) {
        $headerString = 'location: '.BASE_DIR;
        if (!FRIENDLY_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
?>
    <h2 class="one-column-row"><?php echoUcfirstTranslatedString('commands', 1); ?> <?php echoTranslatedString('snack', 2); ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row"><?php echo $response['message']; ?></p>
<?php endif; ?>
<form class="row" method="post">
    <input type="hidden" name="command-name" value="add-snack">
    <div class="one-column-row">
        <label for="snack-name-input"><?php echoUcfirstTranslatedString('commons', 3); ?></label>
        <input type="text" name="name" id="snack-name-input" placeholder="Baiocchi" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} ?>" required>
    </div>
    <div class="options row">
        <div class="first-row">
            <div class="first-column">
                <label for="price-input"><?php echoUcfirstTranslatedString('snack', 3); ?></label>
                <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="99.99" placeholder="3.45" value="<?php if (isset($_POST['price'])) {echo $_POST['price'];} ?>" required>
            </div>
            <div class="column">
                <label for="snacks-per-box-input"><?php echoUcfirstTranslatedString('snack', 4); ?></label>
                <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="999" placeholder="7" value="<?php if (isset($_POST['snacks-per-box'])) {echo $_POST['snacks-per-box'];} ?>" required>
            </div>
            <div class="last-column">
                <label for="expiration-in-days-input"><?php echoTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?></label>
                <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php if (isset($_POST['expiration-in-days'])) {echo $_POST['expiration-in-days'];} ?>" required>
            </div>
        </div>
        <div class="one-column-last-row">
            <label for="countable-input"><?php echoTranslatedString('add-snack', 2); ?></label>
            <input type="checkbox" name="countable" id="countable-input" value="no" <?php if (isset($_POST['countable']) && $_POST['countable']=='no') {echo 'checked';} ?>>
        </div>
    </div>
    <input class="one-column-last-row" type="submit" value="<?php echoUcfirstTranslatedString('commands', 1); ?>">
</form>
<?php require '../echoLibreJS.php'; ?>
<script>
    function askAddSnackConfirm(event) {
        event.preventDefault();
        var confirmString = '<?php echoUcfirstTranslatedString('commands', 1); ?> <?php echoTranslatedString('snack', 2); ?> '+event.target[1].value+'?\n\n<?php echoUcfirstTranslatedString('snack', 3); ?>: '+event.target[2].value+' €.\n<?php echoUcfirstTranslatedString('snack', 4); ?>: '+event.target[3].value+'.\n<?php echoTranslatedString('snack', 5); ?> <?php echoTranslatedString('snack', 6); ?>: '+event.target[4].value+'.\n';
        if (event.target[5].checked) {
            confirmString += '<?php echoTranslatedString('add-snack', 2); ?>';
        } else {
            confirmString += '<?php echoTranslatedString('add-snack', 3); ?>';
        }
        confirmString += '.';
        if (confirm(confirmString)) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askAddSnackConfirm);
</script>
