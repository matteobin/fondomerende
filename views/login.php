<?php
	if (isset($_POST['user-name'])) {
		require_once('send-request.php');
	}
	if (isset($response['response']['status']) && $response['response']['status']==201) {
        header('location: index.php?view=main');
		exit();
	} 
	if (isset($response['response']['message'])): ?> 
	<p>
		<?php echo($response['response']['message']); ?>
	</p>
<?php endif; ?>
<form action="./" method="POST">
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