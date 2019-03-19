<?php
    require_once('process-request.php');
    if (isset($_POST['command-name']) && $_POST['command-name']=='edit-user' && isset($response['response']['status']) && $response['response']['status']==200) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
?>
<div>
    <h2>Edit user</h2>
    <?php if (isset($response['response']['message'])): ?> 
        <p><?php echo($response['response']['message']); ?></p>
    <?php endif; ?>
    <form action="<?php echo(BASE_DIR); ?>index.php?view=edit-user&command-name=get-user-names" method="POST">
        <input type="hidden" name="command-name" value="edit-user">
        <label for="user-name-input">User</label>
        <input type="text" name="name" id="user-name-input" placeholder="artu95_4evah" maxlength="30" value="<?php if (isset($_POST['name'])) {echo($_POST['name']);} else {echo($response['data']['user']['name']);} ?>">
        <label for="friendly-name-input">Friendly name</label>
        <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Arturo" maxlength="60" value="<?php if (isset($_POST['friendly-name'])) {echo($_POST['friendly-name']);} else {echo($response['data']['user']['friendly-name']);} ?>">
        <label for="password-input">Change password</label>
        <input type="password" name="password" id="password-input" placeholder="long is better" maxlength="125" value="<?php if (isset($_POST['password'])) {echo($_POST['password']);} ?>">
        <label for="current-password-input">Write your current password to confirm edits</label>
        <input type="password" name="current-password" id="current-password-input" placeholder="the good ol' one" maxlength="125" required>
        <input type="submit" value="Save">
    </form>
</div>
<script>
    function askEditUserConfirm(event) {
        event.preventDefault();
        if (confirm('Edit user '+event.target[2].value+'?')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askEditUserConfirm);
</script>
