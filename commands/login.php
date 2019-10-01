<?php
function login($name, $password, $rememberUser, $appRequest, $apiCall=true) {
    global $dbManager;
    try {
        if ($apiCall) {
            $dbManager->startTransaction();
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
            $token = bin2hex(random_bytes(12.5)); 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user-id'] = $id;
            $_SESSION['user-token'] = $token;
            $_SESSION['user-friendly-name'] = $friendlyName;
            if (!$appRequest) {
                if ($rememberUser) {
                    setcookie('user-id', $id, time()+86400*5);
                    setcookie('user-token', $token, time()+86400*5);
                    setcookie('user-friendly-name', $friendlyName, time()+86400*5);
                    setcookie('remember-user', true, time()+86400*5);
                } else {
                    setcookie('user-id', $id, 0);
                    setcookie('user-token', $token, 0);
                    setcookie('user-friendly-name', $friendlyName, 0);
                    setcookie('remember-user', false, 0);
                }
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
