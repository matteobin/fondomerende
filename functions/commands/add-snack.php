<?php
function addSnack(DbManager $dbManager, $userId, $name, $price, $snacksPerBox, $expirationInDays, $countable) {
    $subjectUserId = $userId;
    $dbManager->query('INSERT INTO snacks (name, friendly_name, price, snacks_per_box, expiration_in_days, countable) VALUES (?, ?, ?, ?, ?, ?)', array(str_replace(' ', '-', strtolower($name)), $name, $price, $snacksPerBox, $expirationInDays, $countable), 'ssdiii');
    $dbManager->query('SELECT id FROM snacks ORDER BY id DESC LIMIT 1');
    $snackId = 0;
    while ($row = $dbManager->result->fetch_row()) {
        $snackId = $row[0];
    }
    if ($countable) {
        $dbManager->query('INSERT INTO snacks_stock (snack_id) VALUES (?)', array($snackId), 'i');
        $dbManager->query('SELECT id FROM users');
        $usersId = array();
        while ($row = $dbManager->result->fetch_row()) {
            $usersId[] = $row[0];
        }
        foreach($usersId as $userId) {   
            $dbManager->query('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', array($snackId, $userId), 'ii');
        }
    }
    $dbManager->query('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', array($subjectUserId, 5, $snackId), 'iii');
    return array('success'=>true, 'status'=>201, 'data'=>array('snack-id'=>$snackId));
}
