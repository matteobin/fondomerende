<?php
	if (isset($_POST['name'])) {
		require_once('process-request.php');
	}
	if (isset($response['response']['status']) && $response['response']['status']==201) {
        header('location: index.php?view=main&command-name=get-main-view-data');
		exit();
	} 
	if (isset($response['response']['message'])): ?> 
	<p>
		<?php echo($response['response']['message']); ?>
	</p>
<?php endif; ?>
<form action="index.php?view=add-user" method="POST">
    <input type="hidden" name="command-name" value="edit-user">
    <label for="user-name-input">User</label>
    <input type="text" name="name" id="user-name-input" placeholder="artu95_4evah" value="<?php echo($name); ?>">
    <label for="friendly-name-input">Friendly name</label>
    <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Arturo" value="<?php echo($friendlyName); ?>">
    <label for="new-password-input">Change password</label>
    <input type="password" name="new-password" id="new-password-input" placeholder="long is better" value="<?php echo($newValues['password']); ?>">
    <label for="old-password-input">Confirm password</label>
    <input type="password" name="old-password" id="old-password-input" placeholder="the good ol' one" required>
    <input type="submit" value="Add">
</form>