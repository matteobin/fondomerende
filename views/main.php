<?php 
    require_once('process-request.php');
    if (isset($response['response']['message'])) {
        echo($response['response']['message']);
    }
?>
<section>
    <h1>Fund Moolah: <?php echo($response['data']['fund-funds-amount']); ?> €</h1>
</section>
<section>
    <h1>User Moolah: <?php echo($response['data']['user-funds-amount']); ?> €</h1>
</section>
<section>
<h1>Hello <?php echo($_SESSION['user-friendly-name']); ?>!</h1>
    <p>Welcome to the wonderfully edible world of Fondo Merende.<br>Here's a list of tasty activities you can choose from, to start your journey in this sexy web-based office pantry:</p>
    <ul>
        <li>Reach for the wallet to <a href="<?php echo(BASE_DIR); ?>index.php?view=deposit&command-name=get-user-funds"><strong>DEPOSIT</strong></a> some moolah for this very Just Cause.</li>
        <li>Team up with peers and <a href="<?php echo(BASE_DIR); ?>index.php?view=add-snack"><strong>ADD</strong></a> to the list all the junk food you've only been dreaming about.</li>
        <li>Move your lazy butt, get out to <a href="<?php echo(BASE_DIR); ?>index.php?view=buy&command-name=get-to-buy-and-fund-funds"><strong>BUY</strong></a> the damn snacks.</li>
        <li>Done? Now chill: open the fridge and <a href="<?php echo(BASE_DIR); ?>index.php?view=eat&command-name=get-to-eat-and-user-funds"><strong>EAT</strong></a> them all.</li>
    </ul>
    <p>You don't like the world you live in? Maybe it's time to start doing something about it:</p>
    <ul>
        <li>Change what you see in the mirror: <a href="<?php echo(BASE_DIR); ?>index.php?view=edit-user&command-name=get-user-data"><strong>EDIT USER</strong></a>.</li>
        <li>Modify your full fat diet: <a href="<?php echo(BASE_DIR); ?>index.php?view=list-snacks-to-edit&command-name=get-snacks-data"><strong>EDIT SNACKS</strong></a>.</li>
    </ul>
</section>
<section>
    <h1>Neighbourhood happenings:</h1>
    <ul>
        <?php foreach ($response['data']['actions'] as $action): ?>
            <li><?php echo($action); ?></li>
        <?php endforeach; ?>
    </ul>
</section>
<section>
    <h1>Tired of our little community?</h1>
    <p>Log the hell out of here, slut.</p>
    <form action="<?php echo(BASE_DIR); ?>index.php?view=login" method="POST">
        <input type="hidden" name="command-name" value="logout">
        <input type="submit" value="See ya">
    </form>
</section>
