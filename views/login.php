<?php
	if (isset($_POST['command-name'])) {
		require_once('process-request.php');
	}
	if (isset($response['response']['status']) && $response['response']['status']==201) {
        header('location: '.DIR.'index.php?view=main&command-name=get-main-view-data');
		exit();
	} 
	if (isset($response['response']['message'])): ?> 
		<p>
			<?php echo($response['response']['message']); ?>
		</p>
<?php endif; ?>
<form action="<?php echo(DIR); ?>" method="POST">
	<input type="hidden" name="command-name" value="login">
    <label for="user-name-input">User</label>
	<input type="text" name="user-name" placeholder="name" value="<?php if (isset($userName)) {echo($userName);} ?>" required>
    <label for="password-input">Password</label>
	<input type="password" name="password" placeholder="long is better" value="<?php if (isset($password)) {echo($password);} ?>" required>
	<label for="remember-login-checkbox">Remember me</label>
	<input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
	<input type="submit" value="Login">
</form>
<a href="index.php?view=add-user">Add user</a>