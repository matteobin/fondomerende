<?php
	if (isset($_POST['command-name'])) {
		require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';
	}
	if ((isset($response['status']) && $response['status']==201) || isset($_SESSION['user-id'], $_SESSION['user-token'], $_SESSION['user-friendly-name'])) {
        $headerString = 'Location: '.WEB_BASE_DIR;
        if (!CLEAN_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
		exit();
    }
?>
    <h2 class="one-column-row"><?php echo ucfirst(getTranslatedString('login', 1)); ?></h2>
</header>
<form class="row" method="POST">
    <input type="hidden" name="command-name" value="login">
    <div class="row">
        <div class="column">
            <label for="user-name-input"><?php echo ucfirst(getTranslatedString('user', 1)); ?></label>
            <input type="text" id="user-name-input" name="name" placeholder="mighty_pirate90" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} ?>" required>
        </div>
        <div class="column">
            <label for="password-input"><?php echo ucfirst(getTranslatedString('user', 3)); ?></label>
            <input type="password" id="password-input" name="password" placeholder="<?php echo getTranslatedString('user', 4); ?>" value="<?php if (isset($_POST['password'])) {echo $_POST['password'];} ?>" required>
        </div>
    </div>
    <div class="one-column-row">
        <label for="remember-login-checkbox"><?php echo getTranslatedString('login', 3); ?></label>
        <input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
    </div>
    <input class="one-column-row" type="submit" value="<?php echo ucfirst(getTranslatedString('login', 1)); ?>">
</form>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; ?>
<a class="one-column-row" href="<?php echo WEB_BASE_DIR; if (CLEAN_URLS): echo getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1); else: echo 'index.php?view='.getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1); endif; ?>" title="<?php echo getTranslatedString('add-user', 1); ?>"><?php echo ucfirst(getTranslatedString('commands', 1)); ?> <?php echo getTranslatedString('user', 1); ?></a>
