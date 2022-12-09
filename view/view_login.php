<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="css/styles.css" rel="stylesheet" type="text/css"/>
	<title>Tricount</title>
</head>
<body>
	<div class="title">Sign In</div>
	<hr>
	<form action="index/login">
		<input type="text" id="email" name="email" value="<?= $email ?>">
		<input type="password" id="password" name="password" value="<?= $password ?>" >
		<input type="submit" value="Login">
	</form>
	<a href="main/signup">New here ? Click here to join the party !</a>
</body>
</html>