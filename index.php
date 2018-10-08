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
		<form action="init-request.php" method="POST">
            <input type="hidden" name="user-id" value="1">
            <input type="hidden" name="command" value="eat">
            <input type="hidden" name="snack-id" value="3">
            <input type="hidden" name="quantity" value="1">
			<input type="submit" value="Matteo mangia 1 Kinder Bueno">
		</form>
	</div>
</section>
<footer></footer>
</body>
</html>