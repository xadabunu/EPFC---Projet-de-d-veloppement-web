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
            <div class="title" id="t1">Sign Up</div>
            <div class="menu">
                <a href="index.php">Home</a>
            </div>
            <form id="signupform" action="main/signup" method="post">
                <div class="formtitle">Sign Up</div>
                <input id="email" name="email" type="text" size="16" placeholder="Email" value="<?= $email ?>">
                <input id="full_name" name="full_name" type="text" size="16" placeholder="Full Name" value="<?= $full_name ?>">
                <input id="iban" name="iban" type="text" size="16" placeholder="IBAN" value="<?= $iban ?>">
                <input id="password" name="password" type="password" size="16" placeholder="Password" value="<?= $password ?>">
                <input id="password_confirm" name="password_confirm" type="password" size="16" placeholder="Confirm your password" value="<?= $password_confirm ?>">
                <input type="submit" value="Sign Up">
            </form>
            <?php if (count($errors) != 0) : ?>
                <div class='errors'>
                    <br><br>
                    <p>Please correct the following error(s) : </p>
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?=  $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>