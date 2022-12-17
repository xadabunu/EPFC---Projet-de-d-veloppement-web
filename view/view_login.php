<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<base href="<?= $web_root ?>">
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
	<title>Tricount</title>
</head>

<body>
	<div class="main">
		<div class="title" id="t1">Tricount</div>
		<form action="main/login" method="POST">
			<div class="formtitle">Sign In</div>
			<input type="text" id="email" name="email" value="<?= $email ?>">
			<input type="password" id="password" name="password" value="<?= $password ?>">
			<input type="submit" value="Login">
			<a href="index/signup">New here ? Click here to join the party !</a>
		</form>
		<?php if (!(empty($errors))) { ?>
			<ul>
				<?php foreach ($errors as $error) { ?>
					<li><?= $error ?></li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>
</body>

</html>