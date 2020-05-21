<?php
function addSnack($userId, $name, $price, $snacksPerBox, $expirationInDays, $countable) {
    global $dbManager;
    $subjectUserId = $userId;
    $dbManager->query('INSERT INTO snacks (name, friendly_name, price, snacks_per_box, expiration_in_days, countable) VALUES (?, ?, ?, ?, ?, ?)', [str_replace(' ', '-', strtolower($name)], $name, $price, $snacksPerBox, $expirationInDays, $countable), 'ssdiii');
    $dbManager->query('SELECT id FROM snacks ORDER BY id DESC LIMIT 1');
    $snackId = $dbManager->result->fetch_row()[0];
    if ($countable) {
        $dbManager->query('INSERT INTO snacks_stock (snack_id) VALUES (?)', [$snackId], 'i');
        $dbManager->query('SELECT id FROM users');
        while ($row = $dbManager->result->fetch_row()) {
            $usersId[] = $row[0];
        }
        foreach($usersId as $userId) {   
            $dbManager->query('INSERT INTO eaten (snack_id, user_id) VALUES (?, ?)', [$snackId, $userId], 'ii');
        }
    }
    $dbManager->query('INSERT INTO actions (user_id, command_id, snack_id) VALUES (?, ?, ?)', [$subjectUserId, 5, $snackId], 'iii');
    return ['success'=>true, 'status'=>201, 'data'=>['snack-id'=>$snackId]];
}
