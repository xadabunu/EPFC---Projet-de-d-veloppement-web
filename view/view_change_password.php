<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Change Password</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="settings" class="button" id="back">Back</a>
            <p>Change Password</p>
            <button form="changepasswordform" type="submit" class="button save" id="add">Save</button>
        </header>

        <form id="changepasswordform" action="settings/change_password" method="post" class="edit">

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm" aria-hidden="true"></i></span>
                <input name="current_password" type="password" placeholder="Current password" value="" <?php if (array_key_exists('current_wrong_password', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>

            <?php if (array_key_exists('current_wrong_password', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['current_wrong_password']; ?></p>
            <?php } ?>

            <br><br>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm" aria-hidden="true"></i></span>
                <input name="password" type="password" placeholder="New password" value="<?= $password ?>" <?php if (array_key_exists('password_length', $errors) || array_key_exists('password_validity', $errors) || array_key_exists('password_confirm', $errors) || array_key_exists('password_format', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>

            <?php if (array_key_exists('password_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_length']; ?></p>
            <?php }
            if (array_key_exists('password_format', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_format']; ?></p>
            <?php } ?>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm" aria-hidden="true"></i></span>
                <input id="password_confirm" name="password_confirm" type="password" placeholder="Confirm your new password" value="<?= $password_confirm ?>" <?php if (array_key_exists('password_length', $errors) || array_key_exists('password_validity', $errors) || array_key_exists('password_confirm', $errors) || array_key_exists('password_format', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>

            <?php if (array_key_exists('password_confirm', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_confirm']; ?></p>
            <?php } ?>

            <?php if (array_key_exists('password_validity', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_validity']; ?></p>
            <?php } ?>
            
        </form>

    </div>
</body>

</html>