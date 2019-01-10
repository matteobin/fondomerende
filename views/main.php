<?php 
    require_once('process-request.php');
    if (isset($response['response']['message'])) {
        echo($response['response']['message']);
    }
?>
<article>
    <h1>Fund Moolah: <?php echo($response['data']['fund-funds-amount']); ?> €</h1>
</article>
<article>
    <h1>User Moolah: <?php echo($response['data']['user-funds-amount']); ?> €</h1>
</article>
<article>
    <h1>Hello beauty!</h1>
    <p>Welcome to the wonderfully edible world of Fondo Merende.<br>Here's a list of tasty activities you can choose between, to start your journey in this sexy web-based office pantry:</p>
    <ul>
        <li>Reach for the wallet to <a href="index.php?view=deposit&command-name=get-user-funds"><strong>DEPOSIT</strong></a> some moolah for this very Just Cause.</li>
        <li>Team up with peers and <a href="index.php?view=add-snack"><strong>ADD</strong></a> to the list all the junk food you've only been dreaming about.</li>
        <li>Move your lazy butt, get out to <a href="index.php?view=buy&command-name=get-to-buy-and-fund-funds"><strong>BUY</strong></a> the damn snacks.</li>
        <li>Done? Now chill: open the fridge and <a href="index.php?view=eat&command-name=get-to-eat-and-user-funds"><strong>EAT</strong></a> them all.</li>
    </ul>
</article>
<article>
    <h1>Neighbourhood happenings:</h1>
    <ul>
        <?php foreach ($response['data']['actions'] as $action): ?>
            <li><?php echo($action); ?></li>
        <?php endforeach; ?>
    </ul>
</article>
<article>
    <h1>Tired of our little community?</h1>
    <p>Log the hell out of here, slut.</p>
    <form action="./" method="POST">
        <input type="hidden" name="command-name" value="logout">
        <input type="submit" value="See ya">
    </form>
</article>