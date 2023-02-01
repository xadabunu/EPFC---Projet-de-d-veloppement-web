<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit profile</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="settings" class="button" id="back">Back</a>
            <p>Edit profile</p>
            <button form="editprofileform" type="submit" class="button save" id="add">Save</button>
        </header>

        <form id="editprofileform" action="settings/edit_profile" method="post" class="edit">

            <div class="contains_input">
                <span class="icon"><i class="fa-regular fa-at fa-sm" aria-hidden="true"></i></span>
                <input id="email" name="email" type="text" value="<?= $user->email ?>" <?php if (array_key_exists('required', $errors) || array_key_exists('validity', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>

            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('validity', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['validity']; ?></p>
            <?php } ?>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-user fa-sm" aria-hidden="true"></i></span>
                <input id="full_name" name="full_name" type="text" value="<?= $user->full_name ?>" <?php if (array_key_exists('length', $errors) || array_key_exists('name_contains', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>

            <?php if (array_key_exists('length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['length']; ?></p>
            <?php }
            if (array_key_exists('name_contains', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['name_contains']; ?></p>
            <?php } ?>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-credit-card fa-sm" aria-hidden="true"></i></span>
                <input id="iban" name="iban" type="text" placeholder="IBAN - BE12 3456 7890 1234" value="<?= $user->iban ?>" <?php if (array_key_exists('iban', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>

            <?php if (array_key_exists('iban', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['iban']; ?></p>
            <?php } ?>
        </form>
    </div>
</body>

</html>