<?php
	if (isset($_POST['user-name'])) {
		setcookie('auth-key', 'sekrit_PaSSWoRD');
        $jsonResponse = false;
		require_once('send-request.php');
	}
	if (isset($response['response']['status']) && $response['response']['status']==201) {
		header('location: index.php?view=test');
		exit();
	} 
	if (isset($response['response']['message'])): ?> 
	<p>
		<?php echo($response['response']['message']); ?>
	</p>
<?php endif; ?>
<form action="./" method="POST">
	<input type="hidden" name="command-name" value="login">
	<input type="text" name="user-name" placeholder="User" value="<?php if (isset($userName)) {echo($userName);} ?>">
	<input type="password" name="password" placeholder="Password" value="<?php if (isset($password)) {echo($password);} ?>">
	<label for="remember-login-checkbox">Remember me</label>
	<input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
	<input type="submit" value="Login">
</form>
<a href="index.php?view=add-user.php">Add user</a>