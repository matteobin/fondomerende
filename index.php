<?php
	$currentViewName = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
	
	function checkLogin() {
		global $currentViewName;
		$userLogged = false;
		$userToken = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
		$rememberUser = filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN);
		session_start();
		if (isset($_SESSION['users'][$userToken]) && ($currentViewName!='' || $rememberUser)) {
			$userLogged = true;
		}
		return $userLogged;
	}
	
	$views = array(array('name'=>'test', 'path'=>'views/test.php', 'title'=>'Made in App', 'description'=>'Office snack supplies management system for Made in App Fondo Merende.'));
	
	if (checkLogin()) {
		$noView = true;
		foreach ($views as $view) {
			if ($currentViewName==$view['name']) {
				$noView = false;
				$currentView = $view;
			}
		}
		if ($noView) {
			if ($currentViewName=='') {
				$currentView = $views[0];
			} else {
				$currentView = array('name'=>'404', 'path'=>'views/404.php', 'title'=>'404', 'description'=>'Not found.');
			}
		}
	} else {
		$currentView = array('name'=>'login', 'path'=>'views/login.php', 'title'=>'Login', 'description'=>'Fondo Merende authentication form.');
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Fondo Merende | <?php echo($currentView['title']); ?></title>
		<meta name="description" content="<?php echo($currentView['description']); ?>">
		<meta name="author" content="Matteo Bini">
	</head>
	<body>
		<header>
			<h1>Fondo Merende</h1>
		</header>
		<section>
			<?php include_once($currentView['path']); ?>
		</section>
		<footer>
		</footer>
	</body>
</html>
