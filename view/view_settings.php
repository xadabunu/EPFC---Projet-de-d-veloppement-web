<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Settings</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="main" class="button" id="back">Back</a>
            <p>Settings</p>
            <p></p>
        </header>
        <p>Hey <b><?= strlen($user->full_name) > 22 ? substr($user->full_name, 0, 22)."..." : $user->full_name ?></b>!</p>
        <p>I know your email is <span style="color:rgb(214, 51, 132)"><?= strlen($user->email) > 20 ? substr($user->email, 0, 20)."..." : $user->email ?></span>.</p>
        <p>What can I do for you ?</p>
        <a href="settings/edit_profile" class="button bottom bottom2 settings">Edit profile</a>
        <a href="settings/change_password" class="button bottom bottom2 settings">Change password</a>
        <a href="main/logout" class="button bottom bottom2 delete">Logout</a>
    </div>
</body>

</html>