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
		<header class="t1">Tricount</header>
		<form action="main/login" method="POST" class="connect">
			<div class="formtitle">Sign In</div>
			<input class="input" type="text" id="email" name="email" value="<?= $email ?>" <?php if(array_key_exists('empty_email', $errors) || array_key_exists('wrong_email', $errors)) {?>class = "errorInput"<?php } ?>>
			<?php if (array_key_exists('empty_email', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['empty_email'];?></p>
                <?php }
                 if(array_key_exists('wrong_email', $errors)){?>
                    <p class="errorMessage"><?php echo $errors['wrong_email'];?></p>
                <?php } ?>
			<input class="input" type="password" id="password" name="password" value="<?= $password ?>" <?php if(array_key_exists('wrong_password', $errors)) {?>class = "errorInput"<?php } ?>>
			<?php if (array_key_exists('wrong_password', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['wrong_password'];?></p>
                <?php } ?>
			<input class='login' type="submit" value="Login">
			<p class = 'join'><a href="main/signup">New here ? Click here to join the party !</a></p>
		</form>
	</div>
</body>

</html>