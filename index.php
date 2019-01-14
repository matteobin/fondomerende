<?php
    ini_set('display_errors', '1');
	setcookie('auth-key', 'sekrit_PaSSWoRD');
    $currentViewName = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
	
	function checkLogin() {
		global $currentViewName;
		$userLogged = false;
		$userToken = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
		$rememberUser = filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN);
		session_start();
		if (isset($_SESSION['user-logged']) && $_SESSION['user-logged']===true && $_SESSION['user-token']==$userToken && ($currentViewName!='add-user' && $currentViewName!='' || $rememberUser)) {
            $userLogged = true;
		}
		return $userLogged;
	}
	
	$views = array(array('name'=>'login', 'path'=>'views/login.php', 'title'=>'Login', 'description'=>'Fondo Merende authentication form.'), array('name'=>'main', 'path'=>'views/main.php', 'title'=>'Made in App', 'description'=>'Office snack supplies management system for Made in App Fondo Merende.'), array('name'=>'deposit', 'path'=>'views/deposit.php', 'title'=>'Deposit', 'description'=>'It\'s time to put some moolah in your savage digital wallet.'), array('name'=>'add-snack', 'path'=>'views/add-snack.php', 'title'=>'Add', 'description'=>'Add the snack of your dreams to Fondo Merende special reserve.'), array('name'=>'buy', 'path'=>'views/buy.php', 'title'=>'Buy', 'description'=>'Choose wisely what snacks to buy or YOU WILL ALL DIE!'), array('name'=>'eat', 'path'=>'views/eat.php', 'title'=>'Eat', 'description'=>'Our digital pantry, the best part of the software.'), array('name'=>'edit-user', 'path'=>'views/edit-user.php', 'title'=>'Edit user', 'description'=>'Get yourself some plastic surgery!'));
	
	if (checkLogin()) {
		$noView = true;
		foreach ($views as $view) {
			if ($currentViewName==$view['name']) {
				$noView = false;
				$currentView = $view;
                break;
			}
		}
		if ($noView) {
			if ($currentViewName=='') {
				$currentView = $views[1];
			} else {
				$currentView = array('name'=>'404', 'path'=>'views/404.php', 'title'=>'404', 'description'=>'Not found.');
			}
		}
	} else  if ($currentViewName=='add-user') {
        $currentView = array('name'=>'add-user', 'path'=>'views/add-user.php', 'title'=>'Add user', 'description'=>'Fondo Merende add user form.');
    } else {
        $currentView = $views[0];
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
			<?php require_once($currentView['path']); ?>
		</section>
		<footer>
		</footer>
	</body>
</html>
