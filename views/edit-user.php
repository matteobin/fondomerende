<?php
    require BASE_DIR_PATH.'public'.DIRECTORY_SEPARATOR.'process-request.php';
    if (isset($_POST['command-name']) && $_POST['command-name']=='edit-user' && isset($response['status']) && $response['status']==200) {
        $headerString = 'Location: '.WEB_BASE_DIR;
        if (!CLEAN_URLS) {
            $headerString .= 'index.php?view='.getStringInLang('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
?>
    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<form id="edit-user-form" class="row" method="post">
    <input type="hidden" name="command-name" value="edit-user">
    <div class="row">
        <div class="column">
            <label for="user-name-input"><?php echo ucfirst(getStringInLang('commons', 3)); ?></label>
            <input type="text" name="name" id="user-name-input" placeholder="artu95_4evah" maxlength="30" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} else {echo $response['data']['user']['name'];} ?>">
        </div>
        <div class="column">
            <label for="friendly-name-input"><?php echo ucfirst(getStringInLang('user', 2)); ?></label>
            <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Arturo" maxlength="60" value="<?php if (isset($_POST['friendly-name'])) {echo $_POST['friendly-name'];} else {echo $response['data']['user']['friendly-name'];} ?>">
        </div>
    </div>
    <div class="one-column-row">
        <label for="password-input"><?php echo getStringInLang('edit-user', 2); ?> <?php echo getStringInLang('user', 3); ?></label>
        <input type="password" name="password" id="password-input" placeholder="<?php echo getStringInLang('user', 4); ?>" maxlength="125" value="<?php if (isset($_POST['password'])) {echo $_POST['password'];} ?>">
    </div>
    <div class="one-column-row">
        <label for="current-password-input"><?php echo getStringInLang('edit-user', 3); ?> <?php echo getStringInLang('user', 3); ?> <?php echo getStringInLang('edit-user', 4); ?></label>
        <input type="password" name="current-password" id="current-password-input" placeholder="<?php echo getStringInLang('edit-user', 5); ?>" maxlength="125" required>
    </div>
    <input class="one-column-last-row" type="submit" value="<?php echo getStringInLang('commons', 4); ?>">
</form>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row error"><?php echo $response['message']; ?></p>
<?php endif; ?>
<script>
    var translatedStrings = [
        "<?php echo ucfirst(getStringInLang('commands', 2)); ?>",
        "<?php echo getStringInLang('user', 1); ?>"
    ];
</script>
<script src="<?php echo WEB_BASE_DIR; ?>js/edit-user.min.js" defer></script>
