<?php
    require_once('../config.php');
    require_once('../translation.php');
    if (MAINTENANCE) {
        $currentView = array('name'=>'maintenance', 'file-name'=>'maintenance', 'title'=>'Maintenance', 'description'=>'Something big is coming: wait for the update.');
    } else {
        $currentViewName = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);

        function setUserCookieFromSession($sessionIndex) {
            global $_SESSION;
            setCookie($sessionIndex, $_SESSION[$sessionIndex], 0);
        }
        function checkLogin() {
            global $currentViewName;
            $logged = false;
            $idCookie = filter_input(INPUT_COOKIE, 'user-id', FILTER_SANITIZE_STRING);
            $tokenCookie = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
            $friendlyNameCookie = filter_input(INPUT_COOKIE, 'user-friendly-name', FILTER_SANITIZE_STRING);
            $rememberUserCookie = filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN);
            $sessionTokenSet = false;
            if ((isset($_SESSION['user-token']))) {
                $sessionTokenSet = true;
                $logged = true;
                if (!isset($tokenCookie)) {
                    setUserCookieFromSession('user-token');
                }
                if (!isset($idCookie)) {
                    setUserCookieFromSession('user-id');
                }
                if (!isset($friendlyNameCookie)) {
                    setUserCookieFromSession('user-friendly-name');
                }
            } 
            if ($rememberUserCookie && isset($tokenCookie) && isset($idCookie) && isset($friendlyNameCookie)) {
                if (!$sessionTokenSet) {
                    $logged = true;
                    $_SESSION['user-id'] = $idCookie;
                    $_SESSION['user-token'] = $tokenCookie;
                    $_SESSION['user-friendly-name'] = $friendlyNameCookie;
                }
                setcookie('user-id', $idCookie, time()+86400*5);
                setcookie('user-token', $tokenCookie, time()+86400*5);
                setcookie('user-friendly-name', $friendlyNameCookie, time()+86400*5);
                setcookie('remember-user', true, time()+86400*5);
            }
            return $logged;
        }
        $views = array(array('name'=>getTranslatedString('login', 1), 'file-name'=>'login', 'title'=>getTranslatedString('login', 1), 'description'=>getUcfirstTranslatedString('login', 2)), array('name'=>getTranslatedString('main', 1), 'file-name'=>'main', 'title'=>getUcfirstTranslatedString('main', 1), 'description'=>getTranslatedString('main', 2)), array('name'=>getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1), 'file-name'=>'edit-user', 'title'=>getUcfirstTranslatedString('commands', 2).' '.getTranslatedString('user', 1), 'description'=>getTranslatedString('edit-user', 1)), array('name'=>getTranslatedString('commands', 3), 'file-name'=>'deposit', 'title'=>getUcfirstTranslatedString('commands', 3), 'description'=>getTranslatedString('deposit', 1)), array('name'=>getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2), 'file-name'=>'add-snack', 'title'=>getUcfirstTranslatedString('commands', 1).' '.getTranslatedString('snack', 2), 'description'=>getTranslatedString('add-snack', 1)), array('name'=>'edit-snack', 'file-name'=>'edit-snack', 'title'=>'Edit snack', 'description'=>'Change snack name and/or buy default settings.'), array('name'=>'list-snacks-to-edit', 'file-name'=>'list-snacks-to-edit', 'title'=>'Snacks', 'description'=>'Decide what snack to change.'), array('name'=>'buy', 'file-name'=>'buy', 'title'=>'Buy', 'description'=>'Choose wisely what snacks to buy or YOU WILL ALL DIE!'), array('name'=>'eat', 'file-name'=>'eat', 'title'=>'Eat', 'description'=>'Our digital pantry, the best part of the software.'));
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
<html lang="<?php echo($_SESSION['user-lang']); ?>">
	<head>
		<meta charset="utf-8">
        <title>Fondo Merende | <?php echo($currentView['title']); if ($currentView['name']!='login' && $currentView['name']!='add-user' && $currentView['name']!='404') {echo(' - '.$_SESSION['user-friendly-name']);} ?></title>
		<meta name="description" content="<?php echo($currentView['description']); ?>">
		<meta name="author" content="Matteo Bini">
        <meta name="robots" content="noindex, nofollow">
        <style>
            h2 {
                clear: left;
            }
        </style>
	</head>
	<body>
		<header>
			<h1 style="float:left">Fondo Merende</h1><p style="float:left;margin:20px 6px">v1.2.0b</p>
			<?php require_once('../views/'.$currentView['file-name'].'.php'); ?>
		<footer>
		</footer>
	</body>
</html>
