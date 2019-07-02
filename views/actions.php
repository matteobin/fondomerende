    <h2><?php echoUcfirstTranslatedString('actions', 1); ?></h2>
</header>
<?php
    function echoPageHref() {
        global $limit, $_GET;
        echo 'index.php?view=actions&command-name=get-paginated-actions&limit='.$limit;
        if (isset($_GET['asc-order'])) {
            echo '&asc-order='.$_GET['asc-order'];
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
    <a href="<?php echoPageHref(); ?>&page=<?php echo $page-1; ?>"><?php echoTranslatedString('actions', 17); ?></a>
<?php endif; ?>
<?php if ($page<$response['data']['available-pages']): ?>
    <a href="<?php echoPageHref(); ?>&page=<?php echo $page+1; ?>"><?php echoTranslatedString('actions', 18); ?></a>
<?php endif; ?>
<?php elseif (!isset($response['message'])): ?>
    <h3><?php echoTranslatedString('actions', 19); ?></h3>
    <p><?php echoTranslatedString('commons', 6); echoTranslatedString('actions', 20); ?> </p>
<?php endif; ?>
