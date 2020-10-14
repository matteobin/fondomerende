<?php
	require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';
    if (isset($_POST['command-name']) && $_POST['command-name']=='buy' && isset($response['status']) && $response['status']==200) {
        $headerString = 'Location: '.WEB_BASE_DIR;
        if (!CLEAN_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
	if (isset($response['data']['snacks'])) {
		$snacks = $response['data']['snacks'];
        $_SESSION['buy-form-data']['snacks'] = $snacks;
	} else if ($response['status']==200 || $response['status']==400) {
        $snacks = $_SESSION['buy-form-data']['snacks'];
    }
?>
    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php if ($response['status']==404): ?>
    <h3 class="one-column-row"><?php echo getTranslatedString('commons', 5); ?>!</h3>
    <p class="one-column-row"><?php echo getTranslatedString('commons', 6); ?><a href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2); else: echo 'index.php?view='.getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2); endif; ?>" title="<?php echo getTranslatedString('add-snack', 1); ?>"><b><?php echo strtoupper(getTranslatedString('commands', 1)); ?></b></a><?php echo getTranslatedString('commons', 7) ?></p>
<?php elseif ($response['status']==200 || $response['status']==400): ?>
	<form id="buy-form" class="row" method="post">
        <input type="hidden" name="command-name" value="buy">
        <div class="row">
            <select id="snacks-select" class="column" name="id" required>
                <?php foreach($snacks as $snack): ?>
                    <option value="<?php echo $snack['id']; ?>"<?php if (isset($_POST['id']) && $_POST['id']==$snack['id']) {echo 'selected';} ?>><?php echo $snack['friendly_name']; ?></option>
                <?php endforeach; $_SESSION['snacks'] = $snacks; ?> 
            </select>
            <div class="column">
                <label for="quantity-input"><?php echo getTranslatedString('buy', 2); ?></label>
                <input type="number" id="quantity-input" name="quantity" min="1" step="1" max="999" placeholder="1" value="<?php if (isset($_POST['quantity'])) {echo $_POST['quantity'];} ?>" required>
            </div>
        </div>
        <div class="one-column-row">
            <label for="customise-buy-options-input"><?php echo getTranslatedString('buy', 3); ?></label>
            <input type="checkbox" id="customise-buy-options-input" name="customise-buy-options" value="yes" <?php if (isset($_POST['customise-buy-options']) && $_POST['customise-buy-options']=='yes') {echo 'checked';} ?>>
        </div>
        <div id="buy-options" class="options row">
            <div class="first-row last-row">
                <div class="column">
                    <label for="price-input"><?php echo ucfirst(getTranslatedString('snack', 3)); ?></label>
                    <input type="number" id="price-input" name="price" min="0.01" step="0.01" max="99.99" value="<?php if (isset($_POST['price'])) {echo $_POST['price'];} ?>">
                </div>
                <div class="column">
                    <label for="snacks-per-box-input"><?php echo ucfirst(getTranslatedString('snack', 4)); ?></label>
                    <input type="number" id="snacks-per-box-input" name="snacks-per-box" min="1" step="1" max="999" value="<?php if (isset($_POST['snacks-per-box'])) {echo $_POST['snacks-per-box'];} ?>">
                </div>
                <div class="column">
                    <label for="expiration-input"><?php echo getTranslatedString('snack', 5); ?></label>
                    <input type="date" id="expiration-input" name="expiration" min="<?php echo (new DateTime())->format('Y-m-d'); ?>" max="<?php echo (new DateTime('+10000 days'))->format('Y-m-d'); ?>" value="<?php if (isset($_POST['expiration'])) {echo $_POST['expiration'];} ?>">
                </div>
            </div>
        </div>
        <input class="one-column-last-row" type="submit" value="<?php echo ucfirst(getTranslatedString('commands', 5)); ?>">
	</form>
	<script>
        var snacks = <?php echo json_encode($snacks); ?>;
        var translatedStrings = [
            "<?php echo getTranslatedString('number-separators', 1); ?>",
            "<?php echo getTranslatedString('number-separators', 2); ?>",
            "<?php echo getTranslatedString('buy', 5); ?>",
            "<?php echo getTranslatedString('buy', 4); ?>",
            "<?php echo ucfirst(getTranslatedString('commands', 5)); ?>",
            "<?php echo getTranslatedString('buy', 6); ?>",
            "<?php echo ucfirst(getTranslatedString('snack', 3)); ?>",
            "<?php echo ucfirst(getTranslatedString('snack', 4)); ?>",
            "<?php echo getTranslatedString('snack', 5); ?>"
        ];
	</script>
    <script src="<?php echo WEB_BASE_DIR; ?>js/format-number-string.min.js" async></script>
    <script src="<?php echo WEB_BASE_DIR; ?>js/buy.min.js" defer></script>
<?php endif; if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif;
