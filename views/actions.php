    <h2><?php echoUcfirstTranslatedString('actions', 1); ?></h2>
</header>
<?php
    function echoPageHref($limit, $page) {
        global $limit;
        echo BASE_DIR;
        if (FRIENDLY_URLS) {
            echo getTranslatedString('actions', 1).'/'.$limit;
        } else {
            echo 'index.php?view='.getTranslatedString('actions', 1).'&command-name=get-paginated-actions&limit='.$limit;
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
    if (isset($response['message'])) {
        echo $response['message'];
    }
    if (isset($response['data']['actions'])):
?>
    <ol>
    <?php foreach($response['data']['actions'] as $action): ?>
        <li><?php echo $action; ?></li>
    <?php endforeach; ?>
    </ol>
<?php if ($page>1 && $page<=$response['data']['available-pages']): ?>
    <a href="<?php echoPageHref($limit, $page-1); ?>"><?php echoTranslatedString('actions', 17); ?></a>
<?php endif; ?>
<?php if ($page<$response['data']['available-pages']): ?>
    <a href="<?php echoPageHref($limit, $page+1); ?>"><?php echoTranslatedString('actions', 18); ?></a>
<?php endif; ?>
<?php elseif (!isset($response['message'])): ?>
    <h3><?php echoTranslatedString('actions', 19); ?></h3>
    <p><?php echoTranslatedString('commons', 6); echoTranslatedString('actions', 20); ?> </p>
<?php endif; ?>
