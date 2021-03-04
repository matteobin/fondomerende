<?php
function login(DbManager $dbManager, $name, $password, $rememberUser, $apiCall=true) {
    if (!$dbManager->transactionBegun) {
        $dbManager->beginTransaction();
    }
    $dbManager->query('SELECT id, password, friendly_name FROM users WHERE name=?', array($name), 's');
    $hashedPassword = '';
    while ($row = $dbManager->result->fetch_assoc()) {
        $id = $row['id'];
        $hashedPassword = $row['password'];
        $friendlyName = $row['friendly_name'];
    }
    if (password_verify($password, $hashedPassword)) {
        $dbManager->query('UPDATE users SET password=? WHERE id=?', array(password_hash($password, PASSWORD_DEFAULT), $id), 'si');
        do {
            $token = bin2hex(random_bytes(16));
            $dbManager->query('SELECT user_id FROM tokens WHERE token=?', array($token), 's');
            $notUniqueToken = false;
            while ($row = $dbManager->result->fetch_row()) {
                $notUniqueToken = (bool)$row[0];
            }
        } while ($notUniqueToken);
        $_SESSION['user-id'] = $id;
        $_SESSION['user-friendly-name'] = $friendlyName;
        $_SESSION['token'] = $token;
        $device = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
        $tokenExpires = null;
        if (!API_REQUEST) {
            if ($rememberUser) {
                $cookieExpires = time()+432000; // it expires in 5 days
            } else {
                $cookieExpires = 0;
                $tokenExpires = new DateTime('+5 days');
                $tokenExpires = $tokenExpires->format('Y-m-d H:i:s');
            }
            require FUNCTIONS_PATH.'set-fm-cookie.php';
            setFmCookie('token', $token, $cookieExpires);
        }
        $dbManager->query('INSERT INTO tokens (user_id, token, device, expires_at, api_request) VALUES (?, ?, ?, ?, ?)', array($id, $token, $device, $tokenExpires, API_REQUEST), 'isssi');
        if ($apiCall) {
            $response = array('success'=>true, 'status'=>201);
            if (API_REQUEST) {
                $response['data'] = array('token'=>$token);   
            }
        }
    } else if ($apiCall) {
        $response = array('success'=>false, 'status'=>401, 'message'=>getStringInLang('response-messages', 28));
    }
    if ($apiCall) {
        return $response;
    } else {
        return $token;
    }
}
