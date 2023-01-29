<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8">
    <title>Change Password</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="main">
    <header class="t2">
            <a href="settings" class="button" id="back">Back</a>
            <p>Change Password</p>
            <button form= "changepasswordform" type="submit" class ="button save" id="add">Save</button>
    </header>

    <form id="changepasswordform" action="settings/change_password" method="post" class="edit">

        <div class="contains_input">
		    <span class="icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
            <input id="password" name="password" type="password" placeholder="Password" value="<?= $password ?>" <?php if(array_key_exists('password_lenght', $errors) || array_key_exists('password_confirm', $errors) || array_key_exists('password_format', $errors)) {?>class = "errorInput"<?php } ?>>
        </div>

        <?php if (array_key_exists('password_lenght', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['password_lenght'];?></p>
        <?php }
            if (array_key_exists('password_format', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['password_format'];?></p>
        <?php } ?>


        <div class="contains_input">
			<span class="icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
            <input id="password_confirm" name="password_confirm" type="password" placeholder="Confirm your password" value="<?= $password_confirm ?>" <?php if(array_key_exists('password_confirm', $errors) || array_key_exists('password_confirm', $errors)) {?>class = "errorInput"<?php } ?>>
        </div>

        <?php if (array_key_exists('password_confirm', $errors)){ ?>
            <p class="errorMessage"><?php echo $errors['password_confirm'];?></p>
        <?php } ?>
        
        
    </form>

    </div>
</body>
</html>