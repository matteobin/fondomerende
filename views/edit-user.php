<?php
    require_once('process-request.php');
    if (isset($response['data']['name'])) {
        $name = $response['data']['name'];
        $friendlyName = $response['data']['friendly-name'];
    } else if (isset($newValues['name'])) {
        var_dump($newValues);
        $name = $newValues['name'];
        $friendlyName = $newValues['friendly_name'];
    } else {
        $name = $_SESSION['user-name'];
        $friendlyName = $_SESSION['user-friendly-name'];
    }
    if (isset($response['response']['message'])) { ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
<?php

    }
	if (isset($_POST['command-name']) && $_POST['command-name']=='edit-user' && isset($response['response']['status']) && $response['response']['status']==200) {
        unset($_SESSION['user-name']);
        unset($_SESSION['user-friendly-name']);
        header('location: index.php?view=main&command-name=get-main-view-data');
		exit();
	}
    $_SESSION['user-name'] = $name;
    $_SESSION['user-friendly-name'] = $friendlyName;
var_dump($response); ?>
<form action="index.php?view=edit-user&command-name=get-user-names" method="POST">
    <input type="hidden" name="command-name" value="edit-user">
    <label for="user-name-input">User</label>
    <input type="text" name="new-name" id="user-name-input" placeholder="artu95_4evah" value="<?php echo($name); ?>">
    <label for="friendly-name-input">Friendly name</label>
    <input type="text" name="new-friendly-name" id="friendly-name-input" placeholder="Arturo" value="<?php echo($friendlyName); ?>">
    <label for="new-password-input">Change password</label>
    <input type="password" name="new-password" id="new-password-input" placeholder="long is better">
    <label for="old-password-input">Write your current password to confirm edits</label>
    <input type="password" name="old-password" id="old-password-input" placeholder="the good ol' one" required>
    <input type="submit" value="Save">
</form>