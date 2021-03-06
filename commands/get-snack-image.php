<?php
function downloadImageFromGoogle($name) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.it/search?q='.str_replace('-', '+', $name).'&tbm=isch&tbs=isz:i');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (($result = curl_exec($ch))!==false) {
        $needle = 'https://encrypted-tbn0.gstatic.com/images?q=';
        $needleLength = strlen($needle);
        $offset = 0;
        $images = array();
        while ($startPosition = strpos($result, $needle, $offset)) {
            $offset = $startPosition + $needleLength;
            $images[] = substr($result, $startPosition, strpos($result, '"', $offset)-$startPosition);
        }
        if (!empty($images)) {
            if (!file_exists(SNACK_IMAGES_PATH_FROM_PUBLIC_DIR)) {
                echo 'ora';
                mkdir(SNACK_IMAGES_PATH_FROM_PUBLIC_DIR, 0744, true);
            }
            file_put_contents(SNACK_IMAGES_PATH_FROM_PUBLIC_DIR.$name.'.'.IMAGES_EXTENSION, file_get_contents($images[rand(0, count($images)-1)]));        
        }
    }
}

function getSnackImage($name, $overwrite) {
    try {
        $path = SNACK_IMAGES_PATH_FROM_PUBLIC_DIR.$name.'.'.IMAGES_EXTENSION;
        if (true && ($overwrite || !file_exists($path))) {
            downloadImageFromGoogle($name);
        }
        if (APCU_INSTALLED) {
            $cacheKey = 'fm-'.$name.'-snack-image';
            if (apcu_exists($cacheKey)) {
                $response = apcu_fetch($cacheKey);
            } else {
                $response = file_get_contents($path);
                apcu_add($cacheKey, $response);
            }
        } else {
            $response = file_get_contents($path);
        }
    } catch (Exception $exception) {
        $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
