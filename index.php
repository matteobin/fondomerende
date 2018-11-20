<!doctype html>
<html lang="it">
<head>
<meta charset="utf-8">
<title>Fondo Merende | Made in App</title>
<meta name="description" content="Interfaccia per la gestione dei viveri del Fondo Merende Made in App.">
<meta name="author" content="Matteo Bini">
</head>
<body>
<header>
	<h1>Fondo Merende</h1>
</header>
<section>
	<div>
		<form action="send-request.php" method="POST">
            <input type="hidden" name="user-name" value="matteobin">
            <input type="hidden" name="command-name" value="eat">
            <input type="hidden" name="snack-name" value="kinder bueno">
            <input type="hidden" name="quantity" value="1">
			<input type="submit" value="Matteo mangia 1 Kinder Bueno">
		</form>
        <form action="send-request.php" method="POST">
            <input type="hidden" name="user-name" value="matteobin">
            <input type="hidden" name="command-name" value="buy">
            <input type="hidden" name="snack-name" value="kinder bueno">
            <input type="hidden" name="quantity" value="1">
            <input type="hidden" name="price" value="1.5">
			<input type="submit" value="Matteo compra 1 confezione di Kinder Bueno">
		</form>
        <form action="send-request.php" method="POST">
            <input type="hidden" name="user-name" value="matteobin">
            <input type="hidden" name="command-name" value="deposit">
            <input type="hidden" name="amount" value="5.29">
			<input type="submit" value="Matteo deposita 5.29">
		</form>
        <form action="send-request.php" method="POST">
            <input type="hidden" name="user-name" value="matteobin">
            <input type="hidden" name="command-name" value="add snack">
            <input type="hidden" name="name" value="Oreo">
            <input type="hidden" name="price" value="7.07">
            <input type="hidden" name="snacks-per-box" value="7">
            <input type="hidden" name="is-liquid" value="0">
            <input type="hidden" name="expiration-in-days" value="700">
			<input type="submit" value="Matteo aggiunge Oreo">
		</form>
        <form action="send-request.php" method="POST">
            <input type="hidden" name="user-name" value="matteobin">
            <input type="hidden" name="command-name" value="edit snack">
            <input type="hidden" name="snack-name" value="oreo">
            <input type="hidden" name="new-name" value="Oreo - nuovo nome">
            <input type="hidden" name="new-price" value="5.00">
			<input type="submit" value="Matteo rinomina Oreo in Oreo - nuovo nome">
		</form>
        <form action="send-request.php" method="POST">
            <input type="hidden" name="command-name" value="add user">
            <input type="hidden" name="name" value="pk9rocco">
            <input type="hidden" name="password" value="rocchino">
            <input type="hidden" name="friendly-name" value="Roberto Rocchini">
			<input type="submit" value="Crea l'utente di Roberto R.">
		</form>
        <form action="send-request.php" method="POST">
            <input type="hidden" name="user-name" value="pk9rocco">
            <input type="hidden" name="command-name" value="edit user">
            <input type="hidden" name="old-name" value="pk9rocco">
            <input type="hidden" name="new-name" value="pk9brocco">
			<input type="submit" value="Cambia l'username di pk9rocco in pk9brocco">
		</form>
	</div>
</section>
<footer></footer>
</body>
</html>