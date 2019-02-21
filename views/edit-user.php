<section>
    <h1>Edit user</h1>
    <?php
        require_once('process-request.php');
        function storeUserData($value) {
            $variableName = $value[0].substr(str_replace('-', '', mb_convert_case($value, MB_CASE_TITLE)), 1);
            $valuesName = str_replace('-', '_', $value);
            global $response, ${$variableName}, $values;
            if (isset($response['data'][$value])) {
                ${$variableName} = $response['data'][$value];
            } else if (isset($values[$valuesName])) {
                ${$variableName} = $values[$valuesName];
            } else {
                ${$variableName} = $_SESSION['user-'.$value];
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
            unset($_SESSION['user-name']);
            header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
            exit();
        }
        $_SESSION['user-name'] = $name;
        $_SESSION['user-friendly-name'] = $friendlyName;
    ?>
    <form action="<?php echo(BASE_DIR); ?>index.php?view=edit-user&command-name=get-user-names" method="POST">
        <input type="hidden" name="command-name" value="edit-user">
        <label for="user-name-input">User</label>
        <input type="text" name="name" id="user-name-input" placeholder="artu95_4evah" value="<?php echo($name); ?>">
        <label for="friendly-name-input">Friendly name</label>
        <input type="text" name="friendly-name" id="friendly-name-input" placeholder="Arturo" value="<?php echo($friendlyName); ?>">
        <label for="password-input">Change password</label>
        <input type="password" name="password" id="password-input" placeholder="long is better">
        <label for="current-password-input">Write your current password to confirm edits</label>
        <input type="password" name="current-password" id="current-password-input" placeholder="the good ol' one" required>
        <input type="submit" value="Save">
    </form>
</section>
<script>
    function askEditUserConfirm(event) {
        event.preventDefault();
        if (confirm('Edit user '+event.target[2].value+'?')) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askEditUserConfirm);
</script>
