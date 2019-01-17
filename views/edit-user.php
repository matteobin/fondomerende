<section>
<h1>Edit user</h1>
<?php
    require_once('process-request.php');
    function storeUserData($value) {
        $variableName = str_replace('-', '', mb_convert_case($value, MB_CASE_TITLE));
        $oldAndNewValuesName = str_replace('-', '_', $value);
        global $response, ${'old'.$variableName}, ${'new'.$variableName};
        if (isset($response['data'][$value])) {
            ${'old'.$variableName} = $response['data'][$value];
            ${'new'.$variableName} = $response['data'][$value];
        } else if (isset(${$condition.'Values'}[$oldAndNewValuesName])) {
            ${'old'.$variableName} = $oldValues[$oldAndNewValuesName];
            ${'new'.$variableName} = $newValues[$oldAndNewValuesName];
        } else {
            ${'old'.$variableName} = $_SESSION['user-old-'.$value];
            ${'new'.$variableName} = $_SESSION['user-new-'.$value];
        }
    }
    storeUserData('name');
    storeUserData('friendly-name');
    if (isset($response['response']['message'])) { ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
<?php
    }
	if (isset($_POST['command-name']) && $_POST['command-name']=='edit-user' && isset($response['response']['status']) && $response['response']['status']==200) {
        unset($_SESSION['user-old-name']);
        unset($_SESSION['user-new-name']);
        unset($_SESSION['user-old-friendly-name']);
        unset($_SESSION['user-new-friendly-name']);
        header('location: index.php?view=main&command-name=get-main-view-data');
		exit();
	}
    $_SESSION['user-old-name'] = $oldName;
    $_SESSION['user-new-name'] = $newName;
    $_SESSION['user-old-friendly-name'] = $oldFriendlyName;
    $_SESSION['user-new-friendly-name'] = $newFriendlyName;
?>
<form action="index.php?view=edit-user&command-name=get-user-names" method="POST">
    <input type="hidden" name="command-name" value="edit-user">
    <label for="user-name-input">User</label>
    <input type="hidden" name="old-name" value="<?php echo($oldName); ?>">
    <input type="text" name="new-name" id="user-name-input" placeholder="artu95_4evah" value="<?php echo($newName); ?>">
    <label for="friendly-name-input">Friendly name</label>
    <input type="hidden" name="old-friendly-name" value="<?php echo($oldFriendlyName); ?>">
    <input type="text" name="new-friendly-name" id="friendly-name-input" placeholder="Arturo" value="<?php echo($newFriendlyName); ?>">
    <label for="new-password-input">Change password</label>
    <input type="password" name="new-password" id="new-password-input" placeholder="long is better">
    <label for="old-password-input">Write your current password to confirm edits</label>
    <input type="password" name="old-password" id="old-password-input" placeholder="the good ol' one" required>
    <input type="submit" value="Save">
</form>
</section>