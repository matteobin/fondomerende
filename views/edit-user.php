<section>
<h1>Edit user</h1>
<?php
    require_once('process-request.php');
    function storeUserNames($value) {
        $variableName = $value[0].str_replace('-', '', substr(mb_convert_case($value, MB_CASE_TITLE), 1));
        $newValuesName = str_replace('-', '_', $value);
        global $response, ${$variableName};
        if (isset($response['data'][$value])) {
            ${$variableName} = $response['data'][$value];
        } else if (isset($newValues[$newValuesName])) {
            ${$variableName} = $newValues[$newValuesName];
        } else {
            ${$variableName} = $_SESSION['user-'.$value];
        }
    }
    storeUserNames('name');
    storeUserNames('friendly-name');
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
?>
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
</section>