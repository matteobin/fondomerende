<?php
function login($name, $password, $rememberUser, $appRequest, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();
            $dbManager->runQuery('LOCK TABLES users WRITE');
        }
        $dbManager->runPreparedQuery('SELECT id, password, friendly_name FROM users WHERE name=?', array($name), 's');
        $hashedPassword = '';
        while ($usersRow = $dbManager->getQueryRes()->fetch_assoc()) {
            $id = $usersRow['id'];
            $hashedPassword = $usersRow['password'];
            $friendlyName = $usersRow['friendly_name'];
        }
        if (password_verify($password, $hashedPassword)) {
            $dbManager->runPreparedQuery('UPDATE users SET password=? WHERE id=?', array(password_hash($password, PASSWORD_DEFAULT), $id), 'si');
            $token = bin2hex(random_bytes(17));
            $_SESSION['user-id'] = $id;
            $_SESSION['user-friendly-name'] = $friendlyName;
            $_SESSION['user-token'] = $token;
            if (!$appRequest) {
                if ($rememberUser) {
                    $expires = time()+432000; // it expires in 5 days
                } else {
                    $expires = 0;
                }
                setFmCookie('user-token', $token, $expires);
            }
            if ($apiCall) {
                $response = array('success'=>true, 'status'=>201);
                if ($appRequest) {
                    $response['data'] = array('token'=>$token);   
                }
            }
        } else if ($apiCall) {
            $response = array('success'=>false, 'status'=>401, 'message'=>getTranslatedString('response-messages', 28));
        }
        if ($apiCall) {
            $dbManager->runQuery('UNLOCK TABLES');
            $dbManager->endTransaction();          
        }
    } catch (Exception $exception) {
        if ($apiCall) {
            $dbManager->rollbackTransaction();
            $response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage()); 
        } else {
            throw new Exception($exception->getMessage());
        }
    }
    if ($apiCall) {
        return $response;
    } else {
        return $token;
    }
}
