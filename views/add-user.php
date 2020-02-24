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
    <h2 class="one-column-row"><?php echo $currentView['title']; ?></h2>
</header>
<?php if (isset($response['message'])): ?> 
    <p class="one-column-row"><?php echo $response['message']; ?></p>
<?php endif; ?>
<form class="row" method="post">
    <input type="hidden" name="command-name" value="add-user">
    <div class="row">
        <div class="column">
            <label for="user-name-input"><?php echoUcfirstTranslatedString('commons', 3); ?></label>
            <input type="text" name="name" id="user-name-input" placeholder="mighty_pirate90" value="<?php if (isset($_POST['name'])) {echo $_POST['name'];} ?>" maxlength="30" required>
        </div>
        <div class="column">
            <label for="friendly-name-input"><?php echoUcfirstTranslatedString('user', 2) ?></label>
            <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Guybrush Threepwood" value="<?php if (isset($_POST['friendly-name'])) {echo $_POST['friendly-name'];} ?>" maxlength="60" required>
        </div>
    </div>
    <div class="one-column-row">
        <label for="password-input"><?php echoUcfirstTranslatedString('user', 3); ?></label>
        <input type="password" name="password" id="password-input" placeholder="<?php echoTranslatedString('user', 4); ?>" value="<?php if (isset($_POST['password'])) {echo $_POST['password'];} ?>" maxlength="125" required>
    </div>
    <input class="one-column-last-row" type="submit" value="<?php echoUcfirstTranslatedString('commands', 1); ?>">
</form>
