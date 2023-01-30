<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8">
    <title>Settings</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="main">
    <header class="t2">
            <a href="main" class="button" id="back">Back</a>
            <a></a>
            <p>Settings</p>
    </header>

    
    <p>Hey <b><?= $user->full_name ?></b>!</p>
    <p>I know your email is <span style="color:deeppink"><?= $user->email ?></span>.</p>
    <p>What can I do for you ?</p>


    <a href="settings/edit_profile" class="button bottom2 settings">Edit profile</a>
    <a href="settings/change_password" class="button bottom2 settings" >Change password</a>
    <a href="main/logout" class="button bottom2 delete">Logout</a>



    </div>
</body>
</html>