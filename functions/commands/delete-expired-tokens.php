<?php
function deleteExpiredTokens(DbManager $dbManager, $fromCli=true, $verbose=0) {
    $nowToday = (new DateTime())->format('Y-m-d H:i:s');
    if ($verbose) {
        $query = 'SELECT tokens.id, tokens.token, users.id'; 
        if ($verbose==2) {
            if ($fromCli) {
                echo getTranslatedString('tokens', 1)."{$nowToday}.\n";
            } else {
                $response['data']['now-today'] = $nowToday;
            }
            $query .= ', tokens.device, tokens.expires_at, users.name, users.friendly_name'; 
        }
        $query .= ' FROM tokens JOIN users ON tokens.user_id=users.id WHERE tokens.expires_at IS NOT NULL AND tokens.expires_at<=?'; 
        $dbManager->query($query, array($nowToday), 's');
        $toDelete = array();
        while ($tokensRow = $dbManager->result->fetch_assoc()) {
            $toDelete[] = $tokensRow; 
        }
        $toDeleteNum = count($toDelete);
        if (!$toDeleteNum && $fromCli) {
            echo getTranslatedString('tokens', 2)."\n";
        }
        for ($i=0; $i<$toDeleteNum; $i++) {
            $dbManager->query('DELETE FROM tokens WHERE tokens.id=?', array($toDelete[$i]['id']), 'i');
            if ($fromCli) {
                if (!$i && $verbose==2) {
                    echo "\n";
                }
                echo getTranslatedString('tokens', 3)."{$toDelete[$i]['token']}".getTranslatedString('tokens', 4)."{$toDelete[$i]['id']}";
                if ($verbose==2) {
                    echo " ({$toDelete[$i]['name']}".getTranslatedString('tokens', 5)."{$toDelete[$i]['friendly_name']})".getTranslatedString('tokens', 6)."{$toDelete[$i]['device']}".getTranslatedString('tokens', 6)."{$toDelete[$i]['expires_at']}";
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
        $dbManager->query('DELETE FROM tokens WHERE tokens.expires_at IS NOT NULL AND tokens.expires_at<=?', array($nowToday), 's');
    }
    if ($fromCli) {
        return true;
    } else {
        $reponse['success'] = true;
        $response['status'] = 200;
        return $response;
    }
}

if (php_sapi_name()=='cli') {
    define('BASE_DIR_PATH', realpath(__DIR__.'/../').DIRECTORY_SEPARATOR);
    require BASE_DIR_PATH.'injections'.DIRECTORY_SEPARATOR.'delete-expired-tokens-cli.php';
}
