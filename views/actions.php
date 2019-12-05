    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php
    function echoPageHref($limit, $page) {
        global $limit;
        echo BASE_DIR;
        if (FRIENDLY_URLS) {
            echo getTranslatedString('actions', 1).'/'.$limit.'/'.$page;
        } else {
            echo 'index.php?view='.getTranslatedString('actions', 1).'&command-name=get-paginated-actions&limit='.$limit.'&page='.$page;
        }
        if (isset($_GET['asc-order'])) {
            if (FRIENDLY_URLS) {
                echo '/'.$_GET['asc-order'];
            } else {
                echo '&asc-order='.$_GET['asc-order'];
            }
        }
    }
    require 'process-request.php';
    if (isset($response['message'])):
?>
    <p class="one-column-row"><?php echo $response['message']; ?></p>
<?php endif; if (isset($response['data']['actions'])): ?>
    <ol class="one-column-row">
    <?php foreach($response['data']['actions'] as $action): ?>
        <li><?php echo $action; ?></li>
    <?php endforeach; ?>
    </ol>
<div class="row">
<?php if ($page>1 && $page<=$response['data']['available-pages']): ?>
    <a class="column" href="<?php echoPageHref($limit, $page-1); ?>"><?php echoTranslatedString('actions', 18); ?></a>
<?php endif; ?>
<?php if ($page<$response['data']['available-pages']): ?>
    <a class="column" href="<?php echoPageHref($limit, $page+1); ?>"><?php echoTranslatedString('actions', 19); ?></a>
<?php endif; ?>
</div>
<?php elseif (!isset($response['message'])): ?>
    <h3 class="one-column-row"><?php echoTranslatedString('actions', 20); ?></h3>
    <p class="one-column-row"><?php echoTranslatedString('commons', 6); echoTranslatedString('actions', 21); ?></p>
<?php endif; ?>
