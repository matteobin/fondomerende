<?php
    chdir(dirname(__FILE__).'/../');
    define('API_REQUEST', false);
    $apiRequest = false;
    require 'config.php';
    require 'translation.php';
    if (MAINTENANCE) {
        http_response_code(503);
        $currentView = array('name'=>getTranslatedString('maintenance', 1), 'file-name'=>'maintenance', 'title'=>getUcfirstTranslatedString('maintenance', 1), 'description'=>getTranslatedString('maintenance', 2));
    } else {
        $currentViewName = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
        function checkLogin() {
            $logged = false;
            $sessionSet = false;
            if (isset($_SESSION['user-id'], $_SESSION['user-friendly-name'], $_SESSION['token'])) {
                $sessionSet = true;
                $logged = true;
            }
            if (isset($_COOKIE['token'])) {
                if (!$sessionSet) {
                    require 'check-token.php';
                    // to do: handle exceptions
                    $logged = checkToken();
                }
                if ($logged && filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN)) {
                    $expires = time()+432000; // it expires in 5 days
                    require 'set-fm-cookie.php';
                    setFmCookie('token', filter_input(INPUT_COOKIE, 'token', FILTER_SANITIZE_STRING), $expires);
                    setFmCookie('remember-user', true, $expires);
                }
            }
            return $logged;
        }
        $views = array(
            array(
                'name'=>getTranslatedString('login', 1),
                'file-name'=>'login',
                'title'=>getUcfirstTranslatedString('login', 1),
                'description'=>getUcfirstTranslatedString('login', 2)
            ),
            array(
                'name'=>getTranslatedString('main', 1),
                'file-name'=>'main',
                'title'=>getUcfirstTranslatedString('main', 1),
                'description'=>getTranslatedString('main', 2)
            ),
            array(
                'name'=>getTranslatedString('commands', 2).'-'.getTranslatedString('user', 1),
                'file-name'=>'edit-user',
                'title'=>getUcfirstTranslatedString('commands', 2).' '.getTranslatedString('user', 1),
                'description'=>getTranslatedString('edit-user', 1)
            ),
            array(
                'name'=>getTranslatedString('commands', 3),
                'file-name'=>'deposit',
                'title'=>getUcfirstTranslatedString('commands', 3),
                'description'=>getTranslatedString('deposit', 1)
            ),
            array(
                'name'=>getTranslatedString('commands', 4),
                'file-name'=>'withdraw',
                'title'=>getUcfirstTranslatedString('commands', 4),
                'description'=>getTranslatedString('withdraw', 1)
            ),
            array(
                'name'=>getTranslatedString('commands', 1).'-'.getTranslatedString('snack', 2),
                'file-name'=>'add-snack',
                'title'=>getUcfirstTranslatedString('commands', 1).' '.getTranslatedString('snack', 2),
                'description'=>getTranslatedString('add-snack', 1)
            ),
            array(
                'name'=>getTranslatedString('commands', 2).'-'.getTranslatedString('snack', 2),
                'file-name'=>'edit-snack',
                'title'=>getUcfirstTranslatedString('commands', 2).' '.getTranslatedString('snack', 2),
                'description'=>getTranslatedString('edit-snack', 1)
            ),
            array(
                'name'=>getTranslatedString('snack', 1),
                'file-name'=>'list-snacks-to-edit',
                'title'=>getUcfirstTranslatedString('snack', 1),
                'description'=>getTranslatedString('list-snacks-to-edit', 1)
            ),
            array(
                'name'=>getTranslatedString('commands', 5),
                'file-name'=>'buy',
                'title'=>getUcfirstTranslatedString('commands', 5),
                'description'=>getTranslatedString('buy', 1)
            ),
            array(
                'name'=>getTranslatedString('commands', 6),
                'file-name'=>'eat',
                'title'=>getUcfirstTranslatedString('commands', 6),
                'description'=>getTranslatedString('eat', 1)
            ),
            array(
                'name'=>getTranslatedString('actions', 1),
                'file-name'=>'actions',
                'title'=>getUcfirstTranslatedString('actions', 1),
                'description'=>getTranslatedString('actions', 2)
            ),
            array(
                'name'=>getTranslatedString('credits', 1),
                'file-name'=>'credits',
                'title'=>getUcfirstTranslatedString('credits', 1),
                'description'=>getTranslatedString('credits', 2)
            )
        );
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
                    if (CLEAN_URLS) {
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
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
    <head>
        <meta charset="utf-8">
        <title>Fondo Merende | <?php if ($currentView['name']!=getTranslatedString('main', 1)): echo $currentView['title']; endif; if ($currentView['name']!=getTranslatedString('maintenance', 1) && $currentView['name']!=getTranslatedString('login', 1) && $currentView['name']!=getTranslatedString('commands', 1).'-'.getTranslatedString('user', 1) && $currentView['name']!='404'): if ($currentView['name']!=getTranslatedString('main', 1)): echo ' - '; endif; echo $_SESSION['user-friendly-name']; endif; ?></title>
        <meta name="description" content="<?php echo $currentView['description']; ?>">
        <meta name="author" content="Matteo Bini">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            <?php 
                function echoResource($name) {
                    switch($name) {
                        case 'css':
                            $path = 'style.min.css';
                            break;
                        case 'librejs-html':
                            $path = 'librejs.html';
                            break;
                        case 'format-number-string-js';
                            $path = 'format-number-string.js';
                            break;
                    }
                    if (APCU_INSTALLED) {
                        if ($name=='librejs-html') {
                            $cacheKey = $name;
                        } else {
                            $cacheKey = 'fm-'.$name;
                        }
                        if (apcu_exists($cacheKey)) {
                            echo apcu_fetch($cacheKey);
                        } else {
                            $file = file_get_contents('style.min.css');
                            apcu_add($cacheKey, $file);
                            echo $file;
                        }
                    } else {
                        echo file_get_contents($path);
                    }
                }
                echoResource('css');
            ?>
        </style>
    </head>
    <body class="row">
        <header class="row">
            <h1 class="one-column-row" style="margin:.75em 0 .25em">Fondo Merende</h1>
            <?php
                require 'views/'.$currentView['file-name'].'.php';
                if (isset($response['status']) && $response['status']!=200) {
                    http_response_code($response['status']);
                }
            ?>
        <?php if ($currentView['file-name']!='maintenance' && $currentView['name']!=404 && $currentView['file-name']!='login'): ?>
            <footer class="row" style="margin-top:2em">
                <?php if ($currentView['file-name']=='main'): ?>
                    <p class="one-column-row"><a href="<?php echo $hrefs[9]; ?>" title="<?php echoTranslatedString('credits', 2); ?>"><?php echoUcfirstTranslatedString('credits', 1); ?></a></p>
                    <p class="one-column-row"><a href="https://www.gnu.org/licenses/gpl-3.0.en.html" title="Freedom like you never GNU."><?php echoTranslatedString('main', 24); ?></a></p>
                <?php elseif ($currentView['file-name']=='add-user'): ?>
                    <p class="one-column-row"><?php echoTranslatedString('commons', 8); ?><a href="<?php echo BASE_DIR; if (!CLEAN_URLS) {echo 'index.php?view=';} echoTranslatedString('login', 1); ?>" title="<?php echoTranslatedString('login', 2); ?>"><?php echoTranslatedString('add-user', 2); ?></a>.</p>
                <?php else: ?>
                    <p class="one-column-row"><?php echoTranslatedString('commons', 8); ?><a href="<?php echo BASE_DIR; ?>" title="<?php echoTranslatedString('main', 2); ?>"><?php echoTranslatedString('commons', 9); ?></a>.</p>
                <?php endif; ?>
            </footer>
        <?php endif; ?>
    </body>
</html>
