<?php
	if (isset($_POST['command-name'])) {
		require 'process-request.php';
	}
	if ((isset($response['status']) && $response['status']==201) || isset($_SESSION['user-id'], $_SESSION['user-token'], $_SESSION['user-friendly-name'])) {
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
<form class="row" method="POST">
    <input type="hidden" name="command-name" value="login">
    <div class="row">
        <div class="column">
            <label for="user-name-input"><?php echoUcfirstTranslatedString('user', 1); ?></label>
            <input type="text" id="user-name-input" name="name" placeholder="mighty_pirate90" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} ?>" required>
        </div>
        <div class="column">
            <label for="password-input"><?php echoUcfirstTranslatedString('user', 3); ?></label>
            <input type="password" id="password-input" name="password" placeholder="long is better" value="<?php if (isset($_POST['password'])) {echo $_POST['password'];} ?>" required>
        </div>
    </div>
    <div class="one-column-row">
        <label for="remember-login-checkbox"><?php echoTranslatedString('login', 3); ?></label>
        <input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
    </div>
    <input class="one-column-row" type="submit" value="<?php echoUcfirstTranslatedString('login', 1); ?>">
</form>
<div class="one-column-row">
    <a href="<?php echo BASE_DIR; if (FRIENDLY_URLS): echo getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1); else: echo 'index.php?view='.getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1); endif; ?>"><?php echoUcfirstTranslatedString('commands', 1); ?> <?php echoTranslatedString('user', 1); ?></a>
</div>
