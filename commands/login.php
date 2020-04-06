<?php
function login($name, $password, $rememberUser, $apiCall=true) {
    global $apiRequest, $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();
            $dbManager->runQuery('LOCK TABLES users WRITE, tokens WRITE');
        }
        $dbManager->runPreparedQuery('SELECT id, password, friendly_name FROM users WHERE name=?', array($name), 's');
        $hashedPassword = '';
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $id = $row['id'];
            $hashedPassword = $row['password'];
            $friendlyName = $row['friendly_name'];
        }
        if (password_verify($password, $hashedPassword)) {
            $dbManager->runPreparedQuery('UPDATE users SET password=? WHERE id=?', array(password_hash($password, PASSWORD_DEFAULT), $id), 'si');
            $notUniqueToken = false;
            do {
                $token = bin2hex(random_bytes(16));
                $dbManager->runPreparedQuery('SELECT user_id FROM tokens WHERE token=? LIMIT 1', array($token), 's');
                $notUniqueToken = (bool)$dbManager->getQueryRes()->fetch_row()[0];
            } while ($notUniqueToken);
            $_SESSION['user-id'] = $id;
            $_SESSION['user-friendly-name'] = $friendlyName;
            $_SESSION['token'] = $token;
            $device = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
            $tokenExpires = null;
            if (!$apiRequest) {
                if ($rememberUser) {
                    $cookieExpires = time()+432000; // it expires in 5 days
                } else {
                    $cookieExpires = 0;
                    $tokenExpires = (new DateTime('+5 days'))->format('Y-m-d H:i:s');
                }
                setFmCookie('token', $token, $cookieExpires);
            }
            $dbManager->runPreparedQuery('INSERT INTO tokens (user_id, token, device, expires_at, api_request) VALUES (?,?,?,?,?)', array($id, $token, $device, $tokenExpires, $apiRequest), 'isssi');
            if ($apiCall) {
                $response = array('success'=>true, 'status'=>201);
                if ($apiRequest) {
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
