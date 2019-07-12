    <h2><?php echoUcfirstTranslatedString('login', 1); ?></h2>
</header>
<?php
	if (isset($_POST['command-name'])) {
		require 'process-request.php';
	}
	if (isset($response['status']) && $response['status']==201) {
        $headerString = 'location: '.BASE_DIR;
        if (!FRIENDLY_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
		exit();
    } else if (isset($response['status']) && $response['status']==200) {
        $headerString = 'location: '.BASE_DIR;
        if (FRIENDLY_URLS) {
            $headerString .= getTranslatedString('login', 1);
        } else {
            $headerString .= 'index.php?view='.getTranslatedString('login', 1);
        }
        header($headerString);
        exit();
    }
?>
<?php if (isset($response['message'])): ?> 
    <p><?php echo $response['message']; ?></p>
<?php endif; ?>
<form action="<?php echo BASE_DIR.'index.php?view=login'; ?>" method="POST">
    <input type="hidden" name="command-name" value="login">
    <label for="user-name-input"><?php echoUcfirstTranslatedString('user', 1); ?></label>
    <input type="text" id="user-name-input" name="name" placeholder="mighty_pirate90" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} ?>" required>
    <label for="password-input"><?php echoUcfirstTranslatedString('user', 3); ?></label>
    <input type="password" id="password-input" name="password" placeholder="long is better" value="<?php if (isset($_POST['password'])) {echo $_POST['password'];} ?>" required>
    <label for="remember-login-checkbox"><?php echoTranslatedString('login', 3); ?></label>
    <input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
    <input type="submit" value="<?php echoUcfirstTranslatedString('login', 1); ?>">
</form>
<a href="<?php echo BASE_URL; if (FRIENDLY_URLS): echo getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1); else: echo 'index.php?view='.getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1); endif; ?>"><?php echoUcfirstTranslatedString('commands', 1); ?> <?php echoTranslatedString('user', 1); ?></a>
