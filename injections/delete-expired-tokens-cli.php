<?php
const API_REQUEST = false;
require BASE_DIR_PATH.'config.php';
session_start();
require BASE_DIR_PATH.'functions'.DIRECTORY_SEPARATOR.'get-translated-string.php';
$verbose = 0;
foreach($argv as $arg) {
    if ($arg=='-v' || $arg=='--verbose') {
        $verbose = 1;
        break;
    } else if ($arg=='-V' || $arg=='--very-verbose') {
        $verbose = 2;
        break;
    }
}
if (MAINTENANCE) {
    echo getStringInLang('maintenance', 5)."\n";
    if ($verbose) {
        echo getStringInLang('maintenance', 6)."\n";
        if ($verbose==2) {
            echo getStringInLang('maintenance', 7).' '.getStringInLang('maintenance', 8)."!\n"; 
        }
    }
} else {
    require BASE_DIR_PATH.'DbManager.php';
    try {
        $dbManager = new DbManager();
        $dbManager->beginTransaction();
        deleteExpiredTokens($dbManager, $verbose);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
