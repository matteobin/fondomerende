<?php
function deleteExpiredTokens($fromCli=true, $verbose=0) {
    global $dbManager;
    try {
        $dbManager->startTransaction();
        $nowToday = (new DateTime())->format('Y-m-d H:i:s');
        $lockQuery = 'LOCK TABLES tokens WRITE';
        if ($verbose) {
            $lockQuery .= ', users READ';
        }
        $dbManager->runQuery($lockQuery);
        if ($verbose) {
            $query = 'SELECT tokens.token, users.id'; 
            if ($verbose==2) {
                if ($fromCli) {
                    echo "Now today is: {$nowToday}.\n";
                } else {
                    $response['data']['now-today'] = $nowToday;
                }
                $query .= ', tokens.device, tokens.expires_at, users.name, users.friendly_name'; 
            }
            $query .= ' FROM tokens JOIN users ON tokens.user_id=users.id WHERE tokens.expires_at IS NOT NULL AND tokens.expires_at<=?'; 
            $dbManager->runPreparedQuery($query, array($nowToday), 's');
            $toDelete = array();
            while ($tokensRow = $dbManager->getQueryRes()->fetch_assoc()) {
                $toDelete[] = $tokensRow; 
            }
            $toDeleteNum = count($toDelete);
            if (!$toDeleteNum && $fromCli) {
                echo "No tokens to delete.\n";
            }
            for ($i=0; $i<$toDeleteNum; $i++) {
                $dbManager->runPreparedQuery('DELETE FROM tokens WHERE tokens.token=?', array($toDelete[$i]['token']), 's');
                if ($fromCli) {
                    if (!$i && $verbose==2) {
                        echo "\n";
                    }
                    echo "Deleted token {$toDelete[$i]['token']} of user id {$toDelete[$i]['id']}";
                    if ($verbose==2) {
                        echo " ({$toDelete[$i]['name']} AKA {$toDelete[$i]['friendly_name']}) with device {$toDelete[$i]['device']}, expired at {$toDelete[$i]['expires_at']}";
                    }
                    echo ".\n";
                } else {
                    $response['data']['deleted-tokens'][$i] = array('token'=>$toDelete[$i]['token'], 'user-id'=>$toDelete[$i]['id']);
                    if ($verbose==2) {
                        $response['data']['deleted-tokens'][$i]['user-name'] = $toDelete[$i]['name'];
                        $response['data']['deleted-tokens'][$i]['user-friendly-name'] = $toDelete[$i]['friendly_name'];
                        $response['data']['deleted-tokens'][$i]['device'] = $toDelete[$i]['device'];
                        $response['data']['deleted-tokens'][$i]['expired-at'] = $toDelete[$i]['expires_at'];
                    } 
                }
            }
        } else {
            $dbManager->runPreparedQuery('DELETE FROM tokens WHERE tokens.expires_at IS NOT NULL AND tokens.expires_at<=?', array($nowToday), 's');
        }
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        if ($fromCli) {
            return true;
        } else {
            $reponse['success'] = true;
            $response['status'] = 200;
            return $response;
        }
    } catch (Exception $exception) {
        if ($fromCli) {
            if ($verbose) {
                echo $exception->getMessage();    
            }
            return false;
        } else {
            return array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
        } 
    }
}

if (php_sapi_name()=='cli') { 
    chdir(dirname(__FILE__).'/../');
    $apiRequest = false;
    require 'config.php';
    $verbose = 0;
    foreach($argv as $arg) {
        if ($arg=='-v' || $arg=='--verbose') {
            $verbose = 1;
            break;
        } else if ($arg=='-vv' || $arg=='--very-verbose') {
            $verbose = 2;
            break;
        }
    }
    if (MAINTENANCE) {
        echo "Fondo Merende is not available at the moment.\n";
        if ($verbose) {
            echo "Please wait for our team of experts to perfom the required updates.\n";
            if ($verbose==2) {
                echo "Don't be an asshole, wait and DO NOT COMPLAIN!\n"; 
            }
        }
    } else {
        require 'DbManager.php';
        $dbManager = new DbManager(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
        deleteExpiredTokens(true, $verbose);
    }
}
