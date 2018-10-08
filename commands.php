<?php
    function eat($userId, $snackId, $quantity) {
        global $dbManager;
        
        $dbManager->runPreparedQuery('SELECT `price-per-snack` FROM crates WHERE `snack-id`=? AND quantity!=0 ORDER BY expiration ASC LIMIT 1', [$snackId], 'i');
        while ($row = $dbManager->getQueryRes()->fetch_assoc()) {
            $pricePerSnack = $row['price-per-snack'];
        }
    }
?>
