<?php require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php'; ?>
    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php if (isset($verbose) && $verbose==2): ?>
    <p class="one-column-row"><?php $nowToday = new DateTime(); $nowToday = $nowToday->format('Y-m-d H:i:s'); echo getTranslatedString('tokens', 1).$nowToday; ?></p>
<?php endif; ?>
<?php if (isset($response['data']) && count($response['data']['deleted-tokens'])): ?>
    <ol class="one-column-row">
        <?php foreach ($response['data']['deleted-tokens'] as $deletedToken): ?>
            <li><?php
                echo getTranslatedString('tokens', 3).$deletedToken['token'].getTranslatedString('tokens', 4).$deletedToken['id'];
                if ($verbose==2) {
                    echo ' ('.$deletedToken['name'].getTranslatedString('tokens', 5).$deletedToken['friendly_name'].')'.getTranslatedString('tokens', 6).$deletedToken['device'].getTranslatedString('tokens', 6).$deletedToken['expires_at'];
                }
                echo '.'; 
            ?><li>
        <?php endforeach; ?>
    </ol>
<?php elseif ($response['status']==200): ?>
    <h3 class="one-column-row"><?php echo getTranslatedString('tokens', 2); ?></h3>
<?php endif; ?>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; ?>
