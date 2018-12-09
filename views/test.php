<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="logout">
	<input type="submit" value="Logout">
</form>
<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="add-user">
	<input type="hidden" name="name" value="pk9rocco">
	<input type="hidden" name="password" value="rocchino">
	<input type="hidden" name="friendly-name" value="Roberto Rocchini">
	<input type="submit" value="Crea l'utente di Roberto R.">
</form>
<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="edit-user">
	<input type="hidden" name="old-name" value="pk9rocco">
	<input type="hidden" name="new-name" value="pk9brocco">
	<input type="submit" value="Cambia l'username in pk9brocco">
</form>
<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="add-snack">
	<input type="hidden" name="name" value="Oreo">
	<input type="hidden" name="price" value="7.07">
	<input type="hidden" name="snacks-per-box" value="7">
	<input type="hidden" name="is-liquid" value="0">
	<input type="hidden" name="expiration-in-days" value="700">
	<input type="submit" value="Aggiungi Oreo">
</form>
<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="edit-snack">
	<input type="hidden" name="snack-name" value="oreo">
	<input type="hidden" name="new-name" value="Oreo - nuovo nome">
	<input type="hidden" name="new-price" value="5.00">
	<input type="submit" value="Rinomina Oreo in Oreo - nuovo nome">
</form>
<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="deposit">
	<input type="hidden" name="amount" value="5.29">
	<input type="submit" value="Versa 5.29">
</form>
<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="buy">
	<input type="hidden" name="snack-name" value="kinder bueno">
	<input type="hidden" name="quantity" value="1">
	<input type="hidden" name="price" value="1.5">
	<input type="submit" value="Compra 1 confezione di Kinder Bueno">
</form>
<form action="send-request.php" method="GET">
	<input type="hidden" name="command-name" value="get-eatable">
	<input type="submit" value="Ricevi commestibile">
</form>
<form action="send-request.php" method="POST">
	<input type="hidden" name="command-name" value="eat">
	<input type="hidden" name="snack-name" value="kinder bueno">
	<input type="hidden" name="quantity" value="1">
	<input type="submit" value="Mangia 1 Kinder Bueno">
</form>