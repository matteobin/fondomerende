<?php
unset($_COOKIE['key']);
require BASE_DIR_PATH.'set-fm-cookie.php';
setFmCookie('key', '', time()-86400);
if (isset($_COOKIE['token'])) {
    unset($_COOKIE['token']);
    setFmCookie('token', '', time()-86400);
}
if (isset($commandName) && $commandName=='get-snack-image' && !is_array($response)) {
    header('Content-Type: image/'.IMG_EXT);
} else {
    if ($response['status']!=200) {
        http_response_code($response['status']);
    }
    header('Content-Type: application/json');
    $response = json_encode($response);
}
echo $response;
