<section>
<h1>Edit snack</h1>
<?php
    require_once('process-request.php');
    function storeSnackData($value) {
        $variableName = $value[0].substr(str_replace('-', '', mb_convert_case($value, MB_CASE_TITLE)), 1);
        $valuesName = str_replace('-', '_', $value);
        global $response, ${$variableName}, $values;
        if (isset($response['data']['snack'][$value])) {
            ${$variableName} = $response['data']['snack'][$value];
        } else if (isset($values[$valuesName])) {
            ${$variableName} = $values[$valuesName];
        } else {
            ${$variableName} = $_SESSION['snack'][$value];
        }
    }
    storeSnackData('id');
    storeSnackData('friendly-name');
    storeSnackData('price');
    storeSnackData('snacks-per-box');
    storeSnackData('expiration-in-days');
    if (isset($response['response']['message'])) { ?> 
        <p>
            <?php echo($response['response']['message']); ?>
        </p>
<?php
    }
	if (isset($_POST['command-name']) && $_POST['command-name']=='edit-snack' && isset($response['response']['status']) && $response['response']['status']==200) {
        unset($_SESSION['snack']);
        header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
		exit();
	}
    $_SESSION['snack']['id'] = $id;
    $_SESSION['snack']['friendly-name'] = $friendlyName;
    $_SESSION['snack']['price'] = $price;
    $_SESSION['snack']['snacks-per-box'] = $snacksPerBox;
    $_SESSION['snack']['expiration-in-days'] = $expirationInDays;
?>
<form action="<?php echo(BASE_DIR); ?>index.php?view=edit-snack&command-name=get-snack-data&snack-name=<?php echo($snackName) ?>" method="POST">
    <input type="hidden" name="command-name" value="edit-snack">
    <input type="hidden" name="snack-id" value="<?php echo($id); ?>">
    <label for="snack-name-input">Name</label>
    <input type="text" name="name" id="snack-name-input" value="<?php echo($friendlyName); ?>" required>
    <label for="price-input">Price</label>
    <input type="number" name="price" id="price-input" min="0.01" step="0.01" max="999.99" placeholder="0.07" value="<?php echo($price); ?>" required>
    <label for="snacks-per-box-input">Snacks per box</label>
    <input type="number" name="snacks-per-box" id="snacks-per-box-input" min="1" step="1" max="99" placeholder="7" value="<?php echo($snacksPerBox); ?>" required>
    <label for="expiration-in-days-input">Expiration in days</label>
    <input type="number" name="expiration-in-days" id="expiration-in-days-input" min="1" step="1" max="9999" placeholder="90" value="<?php echo($expirationInDays); ?>" required>
    <input type="submit" value="Save">
</form>
</section>
