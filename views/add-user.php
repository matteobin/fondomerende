<?php
	setcookie('auth-key', 'sekrit_PaSSWoRD');
	if (isset($_POST['user-name'])) {
		$jsonResponse = false;
		require_once('send-request.php');
	}
?>
<!doctype html>
<html lang="it">
	<head>
		<meta charset="utf-8">
		<title>Fondo Merende | Login</title>
		<meta name="description" content="Fondo Merende Login">
		<meta name="author" content="Matteo Bini">
	</head>
	<body>
		<header>
			<h1>Login</h1>
		</header>
		<?php if (isset($response['response']['message'])): ?> 
			<p>
				<?php echo($response['response']['message']); ?>
			</p>
		<?php endif; ?>
		<form action="login.php" method="POST">
			<input type="hidden" name="command-name" value="login">
			<input type="text" name="user-name" placeholder="User" value="<?php if (isset($userName)) {echo($userName);} ?>">
			<input type="password" name="password" placeholder="Password" value="<?php if (isset($password)) {echo($password);} ?>">
			<label for="remember-login-checkbox">Remember me</label>
			<input type="checkbox" id="remember-login-checkbox" name="remember-user" value="yes">
			<input type="submit" value="Login">
		</form>
		<a href="add-user.php">Add user</a>
		<footer>
		</footer>
	</body>
</html>