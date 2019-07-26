<?php
    require 'process-request.php';
    if (isset($_POST['command-name']) && $_POST['command-name']=='edit-user' && isset($response['status']) && $response['status']==200) {
        $headerString = 'location: '.BASE_DIR;
        if (!FRIENDLY_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
?>
    <h2><?php echoUcfirstTranslatedString('commands', 2); ?> <?php echoTranslatedString('user', 1); ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
    <p><?php echo $response['message']; ?></p>
<?php endif; ?>
<form method="post">
    <input type="hidden" name="command-name" value="edit-user">
    <div class="row">
        <div>
            <label for="user-name-input"><?php echoUcfirstTranslatedString('commons', 3); ?></label>
            <input type="text" name="name" id="user-name-input" placeholder="artu95_4evah" maxlength="30" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} else {echo $response['data']['user']['name'];} ?>">
        </div>
        <div class="last">
            <label for="friendly-name-input"><?php echoUcfirstTranslatedString('user', 2); ?></label>
            <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Arturo" maxlength="60" value="<?php if (isset($_POST['friendly-name'])) {echo $_POST['friendly-name'];} else {echo $response['data']['user']['friendly-name'];} ?>">
        </div>
    </div>
    <div class="row">
        <label for="password-input"><?php echoTranslatedString('edit-user', 2); ?> <?php echoTranslatedString('user', 3); ?></label>
        <input type="password" name="password" id="password-input" placeholder="<?php echoTranslatedString('user', 4); ?>" maxlength="125" value="<?php if (isset($_POST['password'])) {echo $_POST['password'];} ?>">
    </div>
    <div class="row">
        <label for="current-password-input"><?php echoTranslatedString('edit-user', 3); ?> <?php echoTranslatedString('user', 3); ?> <?php echoTranslatedString('edit-user', 4); ?></label>
        <input type="password" name="current-password" id="current-password-input" placeholder="<?php echoTranslatedString('edit-user', 5); ?>" maxlength="125" required>
    </div>
    <div class="row">
        <input type="submit" value="<?php echoTranslatedString('commons', 4); ?>">
    </div>
</form>
<script>
    function askEditUserConfirm(event) {
        event.preventDefault();
        if (confirm('<?php echoUcfirstTranslatedString('commands', 2); ?> <?php echoTranslatedString('user', 1); ?> '+event.target[2].value+'?')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askEditUserConfirm);
</script>
