<?php
if (APCU_INSTALLED) {
    $key = 'librejs-html';
    if (apcu_exists($key)) {
        $librejs = apcu_fetch($key);
    } else {
        $librejs = file_get_contents('../librejs.html');
        apcu_add($key, $librejs);
    }
    unset($key);
} else {
    $librejs = file_get_contents('../librejs.html');
}
echo $librejs;
