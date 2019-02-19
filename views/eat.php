<?php require_once('process-request.php'); ?>
<section>
    <?php
        if (isset($response['response']['message'])): ?> 
            <p>
                <?php echo($response['response']['message']); ?>
            </p>
    <?php 
        elseif (isset($_POST['command-name']) && $_POST['command-name']=='eat' && $response['response']['status']==200):
            header('location: '.BASE_DIR.'index.php?view=main&command-name=get-main-view-data');
            exit();
        endif; 
    ?>
    <h1>Moolah: <?php echo($response['data']['user-funds-amount']) ?> €</h1>
</section>
<section>
    <h1>Pantry</h1>
    <?php foreach($response['data']['snacks'] as $snack): ?>
        <form action="<?php echo(BASE_DIR); ?>index.php?view=eat&command-name=get-to-eat-and-funds" method="POST">
            <input type="hidden" name="command-name" value="eat"></label>
            <label><?php echo($snack['friendly-name']) ?></label>
            <ul>
                <li>Available: <?php echo($snack['quantity']) ?></li>
                <li>Price: <?php echo($snack['price-per-snack']) ?> €</li>
            </ul>
            <input type="hidden" name="id" value="<?php echo($snack['id']) ?>">
            <input type="submit" value="Eat <?php echo($snack['friendly-name']) ?>" class="submit">
        </form>
    <?php endforeach; ?>
</section>
    <script>
    function askEatConfirm(event) {
        event.preventDefault();
        if (confirm('Eat '+event.target.form.childNodes[3].innerText+'?')) {
            event.target.form.submit();
        }
    }
    var submits = document.querySelectorAll('form input[type="submit"]');
    var submitsNumber = submits.length;
    for (var index=0; index<submitsNumber; index++) {
        submits[index].addEventListener('click', askEatConfirm);
    }
</script>
