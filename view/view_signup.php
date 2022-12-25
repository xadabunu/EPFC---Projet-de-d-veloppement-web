<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <title>Sign up</title>
        <base href="<?= $web_root ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <div class="main">
            <header class="t1">Sign Up</header>
            <div class="menu">
                <a href="index.php">Home</a>
            </div>
            <form id="signupform" action="main/signup" method="post">
                <div class="formtitle">Sign Up</div>
                <input id="email" name="email" type="text" size="16" placeholder="Email" value="<?= $email ?>">
                <?php if (array_key_exists('required', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['required'];?></p>
                <?php }
                 if(array_key_exists('validity', $errors)){?>
                    <p class="errorMessage"><?php echo $errors['validity'];?></p>
                <?php } ?>
                <input id="full_name" name="full_name" type="text" size="16" placeholder="Full Name" value="<?= $full_name ?>">
                <?php if (array_key_exists('lenght', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['lenght'];?></p>
                <?php }
                if (array_key_exists('name_contains', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['name_contains'];?></p>
                <?php } ?>
                <input id="iban" name="iban" type="text" size="16" placeholder="IBAN" value="<?= $iban ?>">
                <?php if (array_key_exists('iban', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['iban'];?></p>
                <?php } ?>
                <input id="password" name="password" type="password" size="16" placeholder="Password" value="<?= $password ?>">
                <?php if (array_key_exists('password_lenght', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['password_lenght'];?></p>
                <?php }
                if (array_key_exists('password_format', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['password_format'];?></p>
                <?php } ?>
                <input id="password_confirm" name="password_confirm" type="password" size="16" placeholder="Confirm your password" value="<?= $password_confirm ?>">
                <?php if (array_key_exists('password_confirm', $errors)){ ?>
                    <p class="errorMessage"><?php echo $errors['password_confirm'];?></p>
                <?php } ?>
                <input type="submit" value="Sign Up">
            </form>
        </div>
    </body>
</html>