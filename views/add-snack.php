<?php
    if (isset($_POST['name'])) {
        require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';
    }
    if (isset($response['status']) && $response['status']==201) {
        $headerString = 'location: '.WEB_BASE_DIR;
        if (!CLEAN_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
?>
    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<form id="add-snack-form" class="row" method="post">
    <input type="hidden" name="command-name" value="add-snack">
    <div class="one-column-row">
        <label for="snack-name-input"><?php echo ucfirst(getTranslatedString('commons', 3)); ?></label>
        <input type="text" name="name" id="snack-name-input" placeholder="Baiocchi" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} ?>" required>
    </div>
    <div class="options row">
        <div class="first-row">
            <div class="first-column">
                <label for="price-input"><?php echo ucfirst(getTranslatedString('snack', 3)); ?></label>
                <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="99.99" placeholder="<?php echo number_format(3.45, 2, getTranslatedString('number-separators', 1), getTranslatedString('number-separators', 2)); ?>" value="<?php if (isset($_POST['price'])) {echo $_POST['price'];} ?>" required>
            </div>
            <div class="column">
                <label for="snacks-per-box-input"><?php echo ucfirst(getTranslatedString('snack', 4)); ?></label>
                <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="999" placeholder="7" value="<?php if (isset($_POST['snacks-per-box'])) {echo $_POST['snacks-per-box'];} ?>" required>
            </div>
            <div class="last-column">
                <label for="expiration-in-days-input"><?php echo getTranslatedString('snack', 5); ?> <?php echo getTranslatedString('snack', 6); ?></label>
                <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php if (isset($_POST['expiration-in-days'])) {echo $_POST['expiration-in-days'];} ?>" required>
            </div>
        </div>
        <div class="one-column-last-row">
            <label for="countable-input"><?php echo getTranslatedString('add-snack', 2); ?></label>
            <input type="checkbox" name="countable" id="countable-input" value="no" <?php if (isset($_POST['countable']) && $_POST['countable']=='no') {echo 'checked';} ?>>
        </div>
    </div>
    <input class="one-column-last-row" type="submit" value="<?php echo ucfirst(getTranslatedString('commands', 1)); ?>">
</form>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; ?>
<script>
    var translatedStrings = [
        "<?php echo getTranslatedString('number-separators', 1); ?>",
        "<?php echo getTranslatedString('number-separators', 2); ?>",
        "<?php echo ucfirst(getTranslatedString('commands', 1)); ?>",
        "<?php echo getTranslatedString('snack', 2); ?>",
        "<?php echo ucfirst(getTranslatedString('snack', 3)); ?>",
        "<?php echo ucfirst(getTranslatedString('snack', 4)); ?>",
        "<?php echo getTranslatedString('snack', 5); ?>",
        "<?php echo getTranslatedString('snack', 6); ?>",
        "<?php echo getTranslatedString('add-snack', 2); ?>",
        "<?php echo getTranslatedString('add-snack', 3); ?>"
    ];
</script>
<script src="<?php echo WEB_BASE_DIR; ?>js/format-number-string.min.js" async></script>
<script src="<?php echo WEB_BASE_DIR; ?>js/add-snack.min.js" defer></script>
