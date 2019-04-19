    <h2><?php echoTranslatedString('login', 1); ?></h2>
</header>
<?php
	if (isset($_POST['command-name'])) {
		require_once('process-request.php');
	}
	if (isset($response['response']['status']) && $response['response']['status']==201) {
        header('location: '.BASE_DIR.'index.php?view=home&command-name=get-home-view-data');
		exit();
    } else if (isset($response['response']['status']) && $response['response']['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=login');
        exit();
    }
?>
<?php if (isset($response['response']['message'])): ?> 
    <p><?php echo($response['response']['message']); ?></p>
<?php endif; ?>
<form action="<?php echo(BASE_DIR.'index.php?view=login'); ?>" method="POST">
    <input type="hidden" name="command-name" value="login">
    <label for="user-name-input"><?php echoUcfirstTranslatedString('main', 3); ?></label>
    <input type="text" id="user-name-input" name="name" placeholder="name" value="<?php if (isset($_POST['name'])) {echo($_POST['name']);} ?>" required>
    <label for="password-input"><?php echoUcfirstTranslatedString('edit-user', 4); ?></label>
    <input type="password" id="password-input" name="password" placeholder="long is better" value="<?php if (isset($_POST['password'])) {echo($_POST['password']);} ?>" required>
    <label for="remember-login-checkbox"><?php echoTranslatedString('login', 2); ?></label>
    <input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
    <input type="submit" value="<?php echoTranslatedString('login', 1); ?>">
</form>
<a href="index.php?view=add-user"><?php echoUcfirstTranslatedString('main', 12); ?> <?php echoTranslatedString('main', 3); ?></a>
