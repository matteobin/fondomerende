    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php
    function echoPageHref($limit, $page) {
        global $limit;
        echo WEB_BASE_DIR;
        if (CLEAN_URLS) {
            echo getStringInLang('actions', 1).'/'.$limit.'/'.$page;
        } else {
            echo 'index.php?view='.getStringInLang('actions', 1).'&command-name=get-paginated-actions&limit='.$limit.'&page='.$page;
        }
        if (isset($_GET['asc-order'])) {
            if (CLEAN_URLS) {
                echo '/'.$_GET['asc-order'];
            } else {
                echo '&asc-order='.$_GET['asc-order'];
            }
        }
    }
    require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';
    if (isset($response['message'])):
?>
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; if (isset($response['data']['actions'])): ?>
    <ol class="one-column-row">
    <?php foreach($response['data']['actions'] as $action): ?>
        <li><?php echo $action; ?></li>
    <?php endforeach; ?>
    </ol>
<div class="row">
<?php if ($page>1 && $page<=$response['data']['available-pages']): ?>
    <a class="column" href="<?php echoPageHref($limit, $page-1); ?>"><?php echo getStringInLang('actions', 19); ?></a>
<?php endif; ?>
<?php if ($page<$response['data']['available-pages']): ?>
    <a class="column" href="<?php echoPageHref($limit, $page+1); ?>"><?php echo getStringInLang('actions', 20); ?></a>
<?php endif; ?>
</div>
<?php elseif (!isset($response['message'])): ?>
    <h3 class="one-column-row"><?php echo getStringInLang('actions', 21); ?></h3>
    <p class="one-column-row"><?php echo getStringInLang('commons', 6); echo getStringInLang('actions', 22); ?></p>
<?php endif; ?>
