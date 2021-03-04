<?php
    define('BASE_DIR_PATH', realpath(__DIR__.'/../').DIRECTORY_SEPARATOR);
    const FUNCTIONS_PATH = BASE_DIR_PATH.'functions'.DIRECTORY_SEPARATOR;
    const INJECTIONS_PATH = BASE_DIR_PATH.'injections'.DIRECTORY_SEPARATOR;
    const API_REQUEST = false;
    require BASE_DIR_PATH.'config.php';
    session_start();
    require FUNCTIONS_PATH.'get-string-in-lang.php';
    require FUNCTIONS_PATH.'get-format.php';
    if (MAINTENANCE) {
        http_response_code(503);
        $currentView = array('name'=>getStringInLang('maintenance', 1), 'file-name'=>'maintenance', 'title'=>ucfirst(getStringInLang('maintenance', 1)), 'description'=>getStringInLang('maintenance', 2));
    } else {
        function checkLogin(&$response, &$dbManager) {
            $logged = false;
            $sessionSet = false;
            if (isset($_SESSION['user-id'], $_SESSION['user-friendly-name'], $_SESSION['token'])) {
                $sessionSet = true;
                $logged = true;
            }
            $token = filter_input(INPUT_COOKIE, 'token', FILTER_SANITIZE_STRING);
            if ($token) {
                if (!$sessionSet) {
                    require FUNCTIONS_PATH.'check-token.php';
                    try {
                        $logged = checkToken($response, $dbManager);
                    } catch (Exception $e) {
                        $response = array('success'=>false, 'status'=>500, 'message'=>$e->getMessage());
                    }
                }
                if ($logged && filter_input(INPUT_COOKIE, 'remember-user', FILTER_VALIDATE_BOOLEAN)) {
                    $expires = time()+432000; // it expires in 5 days
                    require FUNCTIONS_PATH.'set-fm-cookie.php';
                    setFmCookie('token', $token, $expires);
                    setFmCookie('remember-user', true, $expires);
                }
            }
            return $logged;
        }
        $views = array(
            array(
                'name'=>getStringInLang('login', 1),
                'file-name'=>'login',
                'title'=>ucfirst(getStringInLang('login', 1)),
                'description'=>getStringInLang('login', 2)
            ),
            array(
                'name'=>getStringInLang('main', 1),
                'file-name'=>'main',
                'title'=>ucfirst(getStringInLang('main', 1)),
                'description'=>getStringInLang('main', 2)
            ),
            array(
                'name'=>getStringInLang('commands', 2).'-'.getStringInLang('user', 1),
                'file-name'=>'edit-user',
                'title'=>ucfirst(getStringInLang('commands', 2)).' '.getStringInLang('user', 1),
                'description'=>getStringInLang('edit-user', 1)
            ),
            array(
                'name'=>getStringInLang('commands', 3),
                'file-name'=>'deposit',
                'title'=>ucfirst(getStringInLang('commands', 3)),
                'description'=>getStringInLang('deposit', 1)
            ),
            array(
                'name'=>getStringInLang('commands', 4),
                'file-name'=>'withdraw',
                'title'=>ucfirst(getStringInLang('commands', 4)),
                'description'=>getStringInLang('withdraw', 1)
            ),
            array(
                'name'=>getStringInLang('commands', 1).'-'.getStringInLang('snack', 2),
                'file-name'=>'add-snack',
                'title'=>ucfirst(getStringInLang('commands', 1)).' '.getStringInLang('snack', 2),
                'description'=>getStringInLang('add-snack', 1)
            ),
            array(
                'name'=>getStringInLang('commands', 2).'-'.getStringInLang('snack', 2),
                'file-name'=>'edit-snack',
                'title'=>ucfirst(getStringInLang('commands', 2)).' '.getStringInLang('snack', 2),
                'description'=>getStringInLang('edit-snack', 1)
            ),
            array(
                'name'=>getStringInLang('snack', 1),
                'file-name'=>'list-snacks-to-edit',
                'title'=>ucfirst(getStringInLang('snack', 1)),
                'description'=>getStringInLang('list-snacks-to-edit', 1)
            ),
            array(
                'name'=>getStringInLang('commands', 5),
                'file-name'=>'buy',
                'title'=>ucfirst(getStringInLang('commands', 5)),
                'description'=>getStringInLang('buy', 1)
            ),
            array(
                'name'=>getStringInLang('commands', 6),
                'file-name'=>'eat',
                'title'=>ucfirst(getStringInLang('commands', 6)),
                'description'=>getStringInLang('eat', 1)
            ),
            array(
                'name'=>getStringInLang('actions', 1),
                'file-name'=>'actions',
                'title'=>ucfirst(getStringInLang('actions', 1)),
                'description'=>getStringInLang('actions', 2)
            ),
            array(
                'name'=>getStringInLang('credits', 1),
                'file-name'=>'credits',
                'title'=>ucfirst(getStringInLang('credits', 1)),
                'description'=>getStringInLang('credits', 2)
            ),
            array(
                'name'=>getStringInLang('js-licenses', 1),
                'file-name'=>'js-licenses',
                'title'=>ucfirst(getStringInLang('js-licenses', 2)),
                'description'=>getStringInLang('js-licenses', 3)
            ),
            array(
                'name'=>getStringInLang('tokens', 8),
                'file-name'=>'delete-expired-tokens',
                'title'=>getStringInLang('tokens', 9),
                'description'=>getStringInLang('tokens', 10)
            )
        );
        $response;
        $dbManager;
        $currentViewName = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
        if (checkLogin($response, $dbManager)) {
            $noView = true;
            foreach ($views as $view) {
                if ($currentViewName==$view['name']) {
                    $noView = false;
                    $currentView = $view;
                    break;
                }
            }
            if ($noView) {
                if ($currentViewName=='' || $currentViewName==getStringInLang('commands', 1).'-'.getStringInLang('user', 1)) {
                    if (CLEAN_URLS) {
                        $currentView = $views[1];
                        $_GET['command-name'] = 'get-main-view-data';
                    } else {
                        header('location: '.WEB_BASE_DIR.'index.php?view='.getStringInLang('main', 1).'&command-name=get-main-view-data');
                    }
                } else {
                    http_response_code(404);
                    $currentView = array('name'=>'404', 'file-name'=>'404', 'title'=>'404', 'description'=>getStringInLang('404', 2));
                }
            }
        } else if ($currentViewName==getStringInLang('commands', 1).'-'.getStringInLang('user', 1)) {
            $currentView = array('name'=>getStringInLang('commands', 1).'-'.getStringInLang('user', 1), 'file-name'=>'add-user', 'title'=>ucfirst(getStringInLang('commands', 1)).' '.getStringInLang('user', 1), 'description'=>getStringInLang('add-user', 1));
        } else if ($currentViewName==getStringInLang('js-licenses', 1)) {
            $currentView = $views[12];
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
        <meta name="author" content="Matteo Bini">
        <meta charset="utf-8">
        <meta name="description" content="<?php echo $currentView['description']; ?>">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?php echo WEB_BASE_DIR; ?>style.min.css">
        <title>Fondo Merende | <?php if ($currentView['name']!=getStringInLang('main', 1)): echo $currentView['title']; endif; if ($currentView['name']!=getStringInLang('maintenance', 1) && $currentView['name']!=getStringInLang('login', 1) && $currentView['name']!=getStringInLang('commands', 1).'-'.getStringInLang('user', 1) && $currentView['name']!='404'): if ($currentView['name']!=getStringInLang('main', 1)): echo ' - '; endif; echo $_SESSION['user-friendly-name']; endif; ?></title>
    </head>
    <body class="row">
        <header class="row">
            <h1 class="one-column-row">Fondo Merende</h1>
            <?php
                require BASE_DIR_PATH.'views'.DIRECTORY_SEPARATOR.$currentView['file-name'].'.php';
                if (isset($response['status']) && $response['status']!=200) {
                    http_response_code($response['status']);
                }
            ?>
        <?php if ($currentView['file-name']!='maintenance' && $currentView['name']!=404 && $currentView['file-name']!='login'): ?>
            <footer class="row">
                <?php if ($currentView['file-name']=='main'): ?>
                    <p class="one-column-row"><a href="<?php echo $hrefs[9]; ?>" title="<?php echo getStringInLang('credits', 2); ?>"><?php echo ucfirst(getStringInLang('credits', 1)); ?></a></p>
                    <p class="one-column-row"><a href="https://www.gnu.org/licenses/agpl-3.0.en.html" title="Freedom like you never GNU."><?php echo getStringInLang('main', 24); ?></a></p>
                <?php elseif ($currentView['file-name']=='add-user'): ?>
                    <p class="one-column-row"><?php echo getStringInLang('commons', 8); ?><a href="<?php echo WEB_BASE_DIR; if (!CLEAN_URLS) {echo 'index.php?view=';} echo getStringInLang('login', 1); ?>" title="<?php echo getStringInLang('login', 2); ?>"><?php echo getStringInLang('add-user', 2); ?></a>.</p>
                <?php else: ?>
                    <p class="one-column-row"><?php echo getStringInLang('commons', 8); ?><a href="<?php echo WEB_BASE_DIR; ?>" title="<?php echo getStringInLang('main', 2); ?>"><?php echo getStringInLang('commons', 9); ?></a>.</p>
                    <?php if ($currentView['file-name']=='deposit'||$currentView['file-name']=='add-snack'||$currentView['file-name']=='buy'||$currentView['file-name']=='eat'||$currentView['file-name']=='withdraw'||$currentView['file-name']=='edit-user'||$currentView['file-name']=='edit-snack'): ?>
                        <a class="one-column-row" href="<?php echo WEB_BASE_DIR; if (!CLEAN_URLS) {echo 'index.php?view=';} echo getStringInLang('js-licenses', 1); ?>" title="<?php echo getStringInLang('js-licenses', 3); ?>" data-jslicense="1"><?php echo getStringInLang('js-licenses', 2); ?></a>
                    <?php endif; ?>
                <?php endif; ?>
            </footer>
        <?php endif; ?>
    </body>
</html>
