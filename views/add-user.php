<?php
   if (isset($_POST['name'])) {
        require 'process-request.php';
    }
    if (isset($response['status']) && $response['status']==201) {
        $headerString = 'location: '.BASE_DIR;
        if (!FRIENDLY_URLS) {
            $headerString .= 'index.php?view='.getTranslatedString('main', 1).'&command-name=get-main-view-data';
        }
        header($headerString);
        exit();
    }
 ?>
     <h2><?php echoUcfirstTranslatedString('commands', 1); ?> <?php echoTranslatedString('user', 1); ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
    <p>
        <?php echo $response['message']; ?>
    </p>
<?php endif; ?>
<form method="post">
    <input type="hidden" name="command-name" value="add-user">
    <label for="user-name-input"><?php echoUcfirstTranslatedString('commons', 3); ?></label>
    <input type="text" name="name" id="user-name-input" placeholder="mighty_pirate90" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} ?>" maxlength="30" required>
    <label for="friendly-name-input"><?php echoUcfirstTranslatedString('user', 2) ?></label>
    <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Guybrush Threepwood" value="<?php if (isset($_POST['friendly-name'])) {echo $_POST['friendly-name'];} ?>" maxlength="60" required>
    <label for="password-input"><?php echoUcfirstTranslatedString('user', 3); ?></label>
    <input type="password" name="password" id="password-input" placeholder="<?php echoTranslatedString('user', 4); ?>" value="<?php if (isset($_POST['password'])) {echo $_POST['password'];} ?>" maxlength="125" required>
    <input type="submit" value="<?php echoUcfirstTranslatedString('commands', 1); ?>">
</form>
