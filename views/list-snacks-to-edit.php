<?php
    require_once('process-request.php');
?>
<article>
    <h1>Snacks</h1>
    <ul>
    <?php foreach($response['data']['snacks'] as $snack): ?>
       <li><?php echo($snack['friendly-name']); ?> <a href="index.php?view=edit-snack&command-name=get-snack-data&name=<?php echo($snack['name']); ?>">EDIT</a></li>
    <?php endforeach; ?>
    </ul>
</article>