<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Your Tricounts</title>
</head>

<body>
    <div class="title">Your Tricounts</div>
    <ul>
        <?php foreach ($data as $tricount) { ?>
            <li class="tricount">
                <div>
                    <p class="title"><?= $tricount->title ?></p>
                    <p class="nfriends"><?php
                                        $id = $tricount->id;
                                        echo "with $subs_number[$id] friend(s)" ?></p>
                    <p><?= $tricount->description ?></p>
                </div>
            </li>
        <?php } ?>
    </ul>
    <a href="logout">Logout</a>
</body>

</html>