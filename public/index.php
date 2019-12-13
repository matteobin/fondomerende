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
        require '../views-array.php';
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
            <?php
                if (APCU_INSTALLED) {
                    if (apcu_exists('fm-css')) {
                        echo apcu_fetch('fm-css');
                    } else {
                        $css = file_get_contents('../style.min.css');
                        apcu_add('fm-css', $css);
                        echo $css;
                    }
                } else {
                    echo file_get_contents('../style.min.css');
                }
            ?>
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
        <?php if ($currentView['name']!=getTranslatedString('maintenance', 1) && $currentView['name']!=404 && $currentView['name']!=getTranslatedString('login', 1)): ?>
            <footer class="row" style="margin-top:2em">
                <?php if ($currentView['name']==getTranslatedString('main', 1)): ?>
                    <p class="one-column-row"><a href="<?php echo $hrefs[9]; ?>"><?php echoUcfirstTranslatedString('credits', 1); ?></a></p>
                    <p class="one-column-row"><a href="https://www.gnu.org/licenses/gpl-3.0.en.html"><?php echoTranslatedString('main', 24); ?></a></p>
                <?php else: ?>
                    <p class="one-column-row"><?php echoTranslatedString('commons', 8); ?><a href="<?php echo BASE_DIR ?>"><?php echoTranslatedString('commons', 9); ?></a>.</p>
                <?php endif; ?>
            </footer>
        <?php endif; ?>
	</body>
</html>
