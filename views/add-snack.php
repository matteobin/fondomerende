<?php
    if (isset($_POST['name'])) {
        require_once('process-request.php');
    }
    if (isset($response['response']['status']) && $response['response']['status']==201) {
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
        exit();
    }
?>
<section>
    <h1>Add snack</h1>
    <?php if (isset($response['response']['message'])): ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
    <?php endif; ?>
    <form action="<?php echo(BASE_DIR); ?>index.php?view=add-snack" method="POST">
        <input type="hidden" name="command-name" value="add-snack">
        <label for="snack-name-input">Snack name</label>
        <input type="text" name="name" id="snack-name-input" placeholder="name" value="<?php if (isset($_POST['name'])) {echo($_POST['name']);} ?>" required>
        <label for="price-input">Price</label>
        <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="99.99" placeholder="0.07" value="<?php if (isset($_POST['price'])) {echo($_POST['price']);} ?>" required>
        <label for="snacks-per-box-input">Snacks per box</label>
        <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="99" placeholder="7" value="<?php if (isset($_POST['snacks-per-box'])) {echo($_POST['snacks-per-box']);} ?>" required>
        <label for="expiration-in-days-input">Expiration in days</label>
        <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php if (isset($_POST['expiration-in-days'])) {echo($_POST['expiration-in-days']);} ?>" required>
        <label for="countable-input">Uncountable</label>
        <input type="checkbox" name="countable" id="countable-input" value="no" <?php if (isset($_POST['countable']) && $_POST['countable']=='no') {echo('checked');} ?>>
        <input type="submit" value="Add">
    </form>
</section>
<script>
    function askAddSnackConfirm(event) {
        event.preventDefault();
        var confirmString = 'Add snack '+event.target[1].value+'?\n\nPrice: '+event.target[2].value+' €.\nSnacks per box: '+event.target[3].value+'.\nExpiration in days: '+event.target[4].value+'.\n';
        if (event.target[5].checked) {
            confirmString += 'Uncountable';
        } else {
            confirmString += 'Countable';
        }
        confirmString += '.'
        if (confirm(confirmString)) {
            event.target.submit();
        }
    }
    document.querySelector('form').addEventListener('submit', askAddSnackConfirm);
</script>
