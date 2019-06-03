<?php
    require_once('../config.php');
    require_once('../translation.php');
    if (MAINTENANCE) {
        $currentView = array('name'=>getTranslatedString('maintenance', 1), 'file-name'=>'maintenance', 'title'=>getUcfirstTranslatedString('maintenance', 1), 'description'=>getTranslatedString('maintenance', 2));
    } else {
        $currentViewName = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
        function checkLogin() {
            $logged = false;
            $idCookie = filter_input(INPUT_COOKIE, 'user-id', FILTER_SANITIZE_STRING);
            $tokenCookie = filter_input(INPUT_COOKIE, 'user-token', FILTER_SANITIZE_STRING);
            $friendlyNameCookie = filter_input(INPUT_COOKIE, 'user-friendly-name', FILTER_SANITIZE_STRING);
            $rememberUserCookie = filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN);
            $sessionTokenSet = false;
            if (isset($_SESSION['user-token'])) {
                $sessionTokenSet = true;
                $logged = true;
                if (!isset($tokenCookie)) {
                    setCookie('user-token', $_SESSION['user-token']);
                }
                if (!isset($idCookie)) {
                    setCookie('user-id', $_SESSION['user-id']);
                }
                if (!isset($friendlyNameCookie)) {
                    setCookie('user-friendly-name', $_SESSION['user-friendly-name']);
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
        $views = array(array('name'=>getTranslatedString('login', 1), 'file-name'=>'login', 'title'=>getTranslatedString('login', 1), 'description'=>getUcfirstTranslatedString('login', 2)), array('name'=>getTranslatedString('main', 1), 'file-name'=>'main', 'title'=>getUcfirstTranslatedString('main', 1), 'description'=>getTranslatedString('main', 2)), array('name'=>getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1), 'file-name'=>'edit-user', 'title'=>getUcfirstTranslatedString('commands', 2).' '.getTranslatedString('user', 1), 'description'=>getTranslatedString('edit-user', 1)), array('name'=>getTranslatedString('commands', 3), 'file-name'=>'deposit', 'title'=>getUcfirstTranslatedString('commands', 3), 'description'=>getTranslatedString('deposit', 1)), array('name'=>getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2), 'file-name'=>'add-snack', 'title'=>getUcfirstTranslatedString('commands', 1).' '.getTranslatedString('snack', 2), 'description'=>getTranslatedString('add-snack', 1)), array('name'=>getTranslatedString('commands', 2).'-'.getTranslatedString('snack', 2), 'file-name'=>'edit-snack', 'title'=>getUcfirstTranslatedString('commands', 2).' '.getTranslatedString('snack', 2), 'description'=>getTranslatedString('edit-snack', 1)), array('name'=>getTranslatedString('snack', 1), 'file-name'=>'list-snacks-to-edit', 'title'=>getUcfirstTranslatedString('snack', 1), 'description'=>getTranslatedString('list-snacks-to-edit', 1)), array('name'=>getTranslatedString('commands', 4), 'file-name'=>'buy', 'title'=>getUcfirstTranslatedString('commands', 4), 'description'=>getTranslatedString('buy', 1)), array('name'=>getTranslatedString('commands', 5), 'file-name'=>'eat', 'title'=>getUcfirstTranslatedString('commands', 5), 'description'=>getTranslatedString('eat', 1)));
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
                    $currentView = array('name'=>'404', 'file-name'=>'404', 'title'=>'404', 'description'=>getTranslatedString('404', 2));
                }
            }
        } else if ($currentViewName=='add-user') {
            $currentView = array('name'=>getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1), 'file-name'=>'add-user', 'title'=>getUcfirstTranslatedString('commands', 1).' '.getTranslatedString('user', 1), 'description'=>getTranslatedString('add-user', 1));
        } else {
            $currentView = $views[0];
        }
    }
?>
<!doctype html>
<html lang="<?php echo($_SESSION['user-lang']); ?>">
	<head>
		<meta charset="utf-8">
        <title>Fondo Merende | <?php echo($currentView['title']); if ($currentView['name']!=getTranslatedString('maintenance', 1) && $currentView['name']!=getTranslatedString('login', 1) && $currentView['name']!=getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1) && $currentView['name']!='404') {echo(' - '.$_SESSION['user-friendly-name']);} ?></title>
		<meta name="description" content="<?php echo($currentView['description']); ?>">
		<meta name="author" content="Matteo Bini">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
