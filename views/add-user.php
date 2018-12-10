<form action="./" method="POST">
    <input type="hidden" name="command-name" value="add-user">
    <label for="user-name-input">User</label>
    <input type="text" name="user-name" id="user-name-input" placeholder="name" value="<?php if (isset($userName)) {echo($userName);} ?>" required>
    <label for="friendly-name-input">Friendly name</label>
    <input type="type" name="frienly-name" id="friendly-name-input" placeholder="Arturo" value="<?php if (isset($friendlyName)) {echo($friendlyName);} ?>" required>
    <label for="password-input">Password</label>
    <input type="password" name="password" id="password-input" placeholder="long is better" value="<?php if (isset($password)) {echo($password);} ?>" required>
    <input type="submit" value="Create">
</form>