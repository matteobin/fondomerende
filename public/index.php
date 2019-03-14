<?php
    require_once('../config.php');
    if (MAINTENANCE) {
        $currentView = array('name'=>'maintenance', 'file-name'=>'maintenance', 'title'=>'Maintenance', 'description'=>'Something big is coming: wait for the update.');
    } else {
        setcookie('auth-key', 'sekrit_PaSSWoRD');
        $currentViewName = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
        
        function checkLogin() {
            global $currentViewName;
            $logged = false;
            $idCookie = filter_input(INPUT_COOKIE, 'user-id', FILTER_SANITIZE_STRING);
            $tokenCookie = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
            $friendlyNameCookie = filter_input(INPUT_COOKIE, 'user-friendly-name', FILTER_SANITIZE_STRING);
            $rememberUserCookie = filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN);
            session_start();
            if ((isset($_SESSION['user-id']) && isset($_SESSION['user-token'])) || (!is_null($idCookie) && !is_null($tokenCookie))) {
                if (!isset($_SESSION['user-id']) || !isset($_SESSION['user-token'])) {
                    $_SESSION['user-id'] = $idCookie;
                    $_SESSION['user-token'] = $tokenCookie;
                    $_SESSION['user-friendly-name'] = $friendlyNameCookie;
                }
                if ($rememberUserCookie) {
                    setCookie('user-id', $idCookie, time()+86400*5);
                    setCookie('user-token', $tokenCookie, time()+86400*5);
                    setCookie('user-friendly-name', $friendlyNameCookie, time()+86400*5);
                    setCookie('remember-user', true, time()+86400*5);
                }
                $logged = true;
            }
            return $logged;
        }
        
        $views = array(array('name'=>'login', 'file-name'=>'login', 'title'=>'Login', 'description'=>'Fondo Merende authentication form.'), array('name'=>'main', 'file-name'=>'main', 'title'=>'Main', 'description'=>'Office snack supplies management system for Made in App Fondo Merende.'), array('name'=>'edit-user', 'file-name'=>'edit-user', 'title'=>'Edit user', 'description'=>'Get yourself some plastic surgery!'), array('name'=>'deposit', 'file-name'=>'deposit', 'title'=>'Deposit', 'description'=>'It\'s time to put some moolah in your savage digital wallet.'), array('name'=>'add-snack', 'file-name'=>'add-snack', 'title'=>'Add', 'description'=>'Add the snack of your dreams to Fondo Merende special reserve.'), array('name'=>'edit-snack', 'file-name'=>'edit-snack', 'title'=>'Edit snack', 'description'=>'Change snack name and buy default settings.'), array('name'=>'list-snacks-to-edit', 'file-name'=>'list-snacks-to-edit', 'title'=>'Snacks', 'description'=>'Decide what snack to change.'), array('name'=>'buy', 'file-name'=>'buy', 'title'=>'Buy', 'description'=>'Choose wisely what snacks to buy or YOU WILL ALL DIE!'), array('name'=>'eat', 'file-name'=>'eat', 'title'=>'Eat', 'description'=>'Our digital pantry, the best part of the software.'));
        
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
                if ($currentViewName=='' || $currentViewName=='add-user') {
                    header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
                } else {
                    http_response_code(404);
                    $currentView = array('name'=>'404', 'file-name'=>'404', 'title'=>'404', 'description'=>'Not found.');
                }
            }
        } else if ($currentViewName=='add-user') {
            $currentView = array('name'=>'add-user', 'file-name'=>'add-user', 'title'=>'Add user', 'description'=>'Fondo Merende add user form.');
        } else {
            $currentView = $views[0];
        }
    }
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
        <title>Fondo Merende | <?php echo($currentView['title']); if ($currentView['name']!='login' && $currentView['name']!='add-user' && $currentView['name']!='404') {echo(' - '.$_SESSION['user-friendly-name']);} ?></title>
		<meta name="author" content="Matteo Bini">
		<meta name="description" content="<?php echo($currentView['description']); ?>">
        <meta name="robots" content="noindex, nofollow">
	</head>
	<body>
		<header>
			<h1 style="float:left">Fondo Merende</h1><h2 style="float:left">&nbsp;v1.1.1b</h2>
		</header>
		<section style="clear:left">
			<?php require_once('../views/'.$currentView['file-name'].'.php'); ?>
		</section>
		<footer>
		</footer>
	</body>
</html>
