<?php
    require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';    
    if (isset($_POST['command-name']) && $_POST['command-name']=='edit-snack' && isset($response['status']) && $response['status']==200) {
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
<form id="edit-snack-form" class="row" method="post">
    <input type="hidden" name="command-name" value="edit-snack">
    <input type="hidden" name="id" value="<?php if (isset($_POST['id'])) {echo $_POST['id'];} else {echo $response['data']['snack']['id'];} ?>">
    <div class="one-column-row">
        <label for="snack-name-input"><?php echo ucfirst(getStringInLang('commons', 3)); ?></label>
        <input type="text" name="name" id="snack-name-input" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} else {echo $response['data']['snack']['friendly-name'];} ?>" required>
    </div>
    <div class="options row">
        <div class="first-row last-row">
            <div class="column">
                <label for="price-input"><?php echo ucfirst(getStringInLang('snack', 3)); ?></label>
                <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="99.99" placeholder="0.07" value="<?php if (isset($_POST['price'])) {echo $_POST['price'];} else {echo $response['data']['snack']['price'];} ?>" required>
            </div>
            <div class="column">
                <label for="snacks-per-box-input"><?php echo ucfirst(getStringInLang('snack', 4)); ?></label>
                <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="999" placeholder="7" value="<?php if (isset($_POST['snacks-per-box'])) {echo $_POST['snacks-per-box'];} else {echo $response['data']['snack']['snacks-per-box'];} ?>" required>
            </div>
            <div class="column">
                <label for="expiration-in-days-input"><?php echo getStringInLang('snack', 5); ?> <?php echo getStringInLang('snack', 6); ?></label>
                <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php if (isset($_POST['expiration-in-days'])) {echo $_POST['expiration-in-days'];} else {echo $response['data']['snack']['expiration-in-days'];} ?>" required>
            </div>
        </div>
    </div>
    <div class="one-column-row">
        <label for="visible-input"><?php echo ucfirst(getStringInLang('snack', 7)); ?></label>
        <input type="hidden" name="visible" value="yes">
        <input type="checkbox" name="visible" id="visible-input" value="no" <?php if (isset($_POST['visible']) && $_POST['visible']=='no') {echo 'checked';} else if (isset($response['data']['snack']['visible']) && $response['data']['snack']['visible']==0) {echo 'checked';} ?>>
    </div>
    <input class="one-column-last-row" type="submit" value="<?php echo getStringInLang('commons', 4); ?>">
</form>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; ?>
<script>
    var translatedStrings = [
        "<?php echo getFormat(1); ?>",
        "<?php echo getFormat(2); ?>",
        "<?php echo ucfirst(getStringInLang('commands', 2)); ?>",
        "<?php echo getStringInLang('snack', 2); ?>",
        "<?php echo ucfirst(getStringInLang('snack', 3)); ?>",
        "<?php echo ucfirst(getStringInLang('snack', 4)); ?>",
        "<?php echo ucfirst(getStringInLang('snack', 5)); ?>",
        "<?php echo getStringInLang('snack', 6); ?>",
        "<?php echo ucfirst(getStringInLang('snack', 7)); ?>",
        "<?php echo ucfirst(getStringInLang('snack', 8)); ?>"
    ];
</script>
<script src="<?php echo WEB_BASE_DIR; ?>js/format-number-string.min.js" async></script>
<script src="<?php echo WEB_BASE_DIR; ?>js/edit-snack.min.js" defer></script>
