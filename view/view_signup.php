<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <title>Sign up</title>
        <base href="<?= $web_root ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css" />
    </head>

    <body>
        <div class="title">Sign Up</div>
        <div class="menu">
            <a href="index.php">Home</a>
        </div>
        <div class="main">
            <form id="signupform" action="main/signup" method="post">
                <table>
                    <tr>
                        <td><input id="email" name="email" type="text" size="16" value="Email"></td>
                    </tr>
                    <tr>
                        <td><input id="full_name" name="full_name" type="text" size="16" value="Full Name"></td>
                    </tr>
                    <tr>
                        <td><input id="iban" name="iban" type="text" size="16" value="IBAN"></td>
                    </tr>
                    <tr>
                        <td><input id="password" name="password" type="text" size="16" value="Password"></td>
                    </tr>
                    <tr>
                        <td><input id="password_confirm" name="password_confirm" type="text" size="16" value="Confirm your password"></td>
                    </tr>
                </table>
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