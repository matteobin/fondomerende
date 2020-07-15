<?php
$key = 'librejs-html';
$path = BASE_DIR_PATH.'resources'.DIRECTORY_SEPARATOR.'librejs.html';
if (APCU_INSTALLED) {
    if (apcu_exists($key)) {
        echo apcu_fetch($key);
    } else {
        $file = file_get_contents($path);
        apcu_add($key, $file);
        echo $file;
    }
} else {
    echo file_get_contents($path);
}
