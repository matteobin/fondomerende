<?php
function addSnack($userId, $name, $price, $snacksPerBox, $expirationInDays, $countable) {
    global $dbManager;
    try {
        $subjectUserId = $userId;
        $dbManager->startTransaction();
        $dbManager->runQuery('LOCK TABLES snacks WRITE, snacks_stock WRITE, users READ, eaten WRITE, actions WRITE');
        $dbManager->runPreparedQuery('INSERT INTO snacks (name, friendly_name, price, snacks_per_box, expiration_in_days, countable) VALUES (?, ?, ?, ?, ?, ?)', array(str_replace(' ', '-', strtolower($name)), $name, $price, $snacksPerBox, $expirationInDays, $countable), 'ssdiii');
        $dbManager->runQuery('SELECT id FROM snacks ORDER BY id DESC LIMIT 1');
        $snackId = $dbManager->getQueryRes()->fetch_row()[0];
        if ($countable) {
            $dbManager->runPreparedQuery('INSERT INTO snacks_stock (snack_id) VALUES (?)', array($snackId), 'i');
            $dbManager->runQuery('SELECT id FROM users');
            while ($row = $dbManager->getQueryRes()->fetch_row()) {
                $usersId[] = $row[0];
            }
            foreach($usersId as $userId) {   
                $dbManager->runPreparedQuery('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', array($snackId, $userId), 'ii');
            }
        }
        $dbManager->runPreparedQuery('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($subjectUserId, 5, $snackId), 'iii');
        $dbManager->runQuery('UNLOCK TABLES');
        $dbManager->endTransaction();
        $response = array('success'=>true, 'status'=>201, 'data'=>array('snack-id'=>$snackId));
    } catch (Exception $exception) {
        $dbManager->rollbackTransaction();
		$response = array('success'=>false, 'status'=>500, 'message'=>$exception->getMessage());
    }
    return $response;
}
