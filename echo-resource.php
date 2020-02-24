<?php
function echoResource($name) {
    switch($name) {
        case 'css':
            $path = '../style.min.css';
            break;
        case 'librejs-html':
            $path = '../librejs.html';
            break;
        case 'format-number-string-js';
            $path = '../format-number-string.js';
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
            $file = file_get_contents('../style.min.css');
            apcu_add($cacheKey, $file);
            echo $file;
        }
    } else {
        echo file_get_contents($path);
    }
}
