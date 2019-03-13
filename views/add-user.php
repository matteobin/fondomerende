
    <?php
        if (isset($_POST['name'])) {
            require_once('process-request.php');
        }
        if (isset($response['response']['status']) && $response['response']['status']==201) {
            unset($_SESSION['form']);
            header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
            exit();
        } ?>
<section>
    <h1>Add user</h1>
    <?php if (isset($response['response']['message'])): ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
    <?php endif; ?>
    <form action="<?php echo(BASE_DIR) ?>index.php?view=add-user" method="POST">
        <input type="hidden" name="command-name" value="add-user">
        <label for="user-name-input">User</label>
        <input type="text" name="name" id="user-name-input" placeholder="artu95_4evah" value="<?php if (isset($_SESSION['form']['name'])) {echo($_SESSION['form']['name']);} ?>" maxlength="30" required>
        <label for="friendly-name-input">Friendly name</label>
        <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Arturo" value="<?php if (isset($_SESSION['form']['friendly-name'])) {echo($_SESSION['form']['friendly-name']);} ?>" maxlength="60" required>
        <label for="password-input">Password</label>
        <input type="password" name="password" id="password-input" placeholder="long is better" value="<?php if (isset($_SESSION['form']['password'])) {echo($_SESSION['form']['password']);} ?>" maxlength="125" required>
        <input type="submit" value="Add">
    </form>
</section>
