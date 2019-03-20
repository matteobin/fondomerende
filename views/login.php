    <h2>Login</h2>
</header>
<?php
	if (isset($_POST['command-name'])) {
		require_once('process-request.php');
	}
	if (isset($response['response']['status']) && $response['response']['status']==201) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
		exit();
    } 
?>
<?php if (isset($response['response']['message'])): ?> 
    <p><?php echo($response['response']['message']); ?></p>
<?php endif; ?>
<form action="<?php echo(BASE_DIR); ?>" method="POST">
    <input type="hidden" name="command-name" value="login">
    <label for="user-name-input">User</label>
    <input type="text" name="name" placeholder="name" value="<?php if (isset($_POST['name'])) {echo($_POST['name']);} ?>" required>
    <label for="password-input">Password</label>
    <input type="password" name="password" placeholder="long is better" value="<?php if (isset($_POST['password'])) {echo($_POST['password']);} ?>" required>
    <label for="remember-login-checkbox">Remember me</label>
    <input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
    <input type="submit" value="Login">
</form>
<a href="index.php?view=add-user">Add user</a>
