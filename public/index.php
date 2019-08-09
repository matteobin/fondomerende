<?php
    require '../config.php';
    require '../translation.php';
    if (MAINTENANCE) {
        http_response_code(503);
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
        $views = array(array('name'=>getTranslatedString('login', 1), 'file-name'=>'login', 'title'=>getUcfirstTranslatedString('login', 1), 'description'=>getUcfirstTranslatedString('login', 2)), array('name'=>getTranslatedString('main', 1), 'file-name'=>'main', 'title'=>getUcfirstTranslatedString('main', 1), 'description'=>getTranslatedString('main', 2)), array('name'=>getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1), 'file-name'=>'edit-user', 'title'=>getUcfirstTranslatedString('commands', 2).' '.getTranslatedString('user', 1), 'description'=>getTranslatedString('edit-user', 1)), array('name'=>getTranslatedString('commands', 3), 'file-name'=>'deposit', 'title'=>getUcfirstTranslatedString('commands', 3), 'description'=>getTranslatedString('deposit', 1)), array('name'=>getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2), 'file-name'=>'add-snack', 'title'=>getUcfirstTranslatedString('commands', 1).' '.getTranslatedString('snack', 2), 'description'=>getTranslatedString('add-snack', 1)), array('name'=>getTranslatedString('commands', 2).'-'.getTranslatedString('snack', 2), 'file-name'=>'edit-snack', 'title'=>getUcfirstTranslatedString('commands', 2).' '.getTranslatedString('snack', 2), 'description'=>getTranslatedString('edit-snack', 1)), array('name'=>getTranslatedString('snack', 1), 'file-name'=>'list-snacks-to-edit', 'title'=>getUcfirstTranslatedString('snack', 1), 'description'=>getTranslatedString('list-snacks-to-edit', 1)), array('name'=>getTranslatedString('commands', 4), 'file-name'=>'buy', 'title'=>getUcfirstTranslatedString('commands', 4), 'description'=>getTranslatedString('buy', 1)), array('name'=>getTranslatedString('commands', 5), 'file-name'=>'eat', 'title'=>getUcfirstTranslatedString('commands', 5), 'description'=>getTranslatedString('eat', 1)), array('name'=>getTranslatedString('actions', 1), 'file-name'=>'actions', 'title'=>getUcfirstTranslatedString('actions', 1), 'description'=>getTranslatedString('actions', 2)), array('name'=>getTranslatedString('credits', 1), 'file-name'=>'credits', 'title'=>getUcfirstTranslatedString('credits', 1), 'description'=>getTranslatedString('credits', 2)));
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
                if ($currentViewName=='' || $currentViewName==getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1)) {
                    if (FRIENDLY_URLS) {
                        $currentView = $views[1];
                        $_GET['command-name'] = 'get-main-view-data';
                    } else {
                        header('location: '.BASE_DIR.'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data');
                    }
                } else {
                    http_response_code(404);
                    $currentView = array('name'=>'404', 'file-name'=>'404', 'title'=>'404', 'description'=>getTranslatedString('404', 2));
                }
            }
        } else if ($currentViewName==getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1)) {
            $currentView = array('name'=>getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1), 'file-name'=>'add-user', 'title'=>getUcfirstTranslatedString('commands', 1).' '.getTranslatedString('user', 1), 'description'=>getTranslatedString('add-user', 1));
        } else {
            $currentView = $views[0];
        }
    }
    function sanitizeOutput($buffer) {
        return preg_replace(array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/'), array('>', '<', '\\1', ''), $buffer);
    }
    ob_start('sanitizeOutput');
?>
<!doctype html>
<html lang="<?php echo $_SESSION['user-lang']; ?>">
	<head>
		<meta charset="utf-8">
        <title>Fondo Merende | <?php if ($currentView['name']!=getTranslatedString('main', 1)): echo $currentView['title']; endif; if ($currentView['name']!=getTranslatedString('maintenance', 1) && $currentView['name']!=getTranslatedString('login', 1) && $currentView['name']!=getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1) && $currentView['name']!='404'): if ($currentView['name']!=getTranslatedString('main', 1)): echo ' - '; endif; echo $_SESSION['user-friendly-name']; endif; ?></title>
		<meta name="description" content="<?php echo $currentView['description']; ?>">
		<meta name="author" content="Matteo Bini">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            <?php echo file_get_contents('../style.min.css'); ?>
        </style>
	</head>
	<body class="row">
        <header class="row">
            <div class="one-column-row">
                <h1 style="margin: 0.5em 0 0">Fondo Merende</h1>
            </div>
            <?php
                require '../views/'.$currentView['file-name'].'.php';
                if (isset($response['status']) && $response['status']!=200) {
                    http_response_code($response['status']);
                }
            ?>
	</body>
</html>
