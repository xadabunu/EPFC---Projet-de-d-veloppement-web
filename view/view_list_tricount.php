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
                    <?php
                    $id = $tricount->id;
                    $n = $subs_number[$id];
                    if ($n > 0) { ?>
                        <p class="participants_number">
                            <?php echo "with $n friend(s)" ?></p>
                    <?php } ?>
                    <p><?= $tricount->description ?></p>
                </div>
            </li>
        <?php } ?>
    </ul>
    <a href="logout">Logout</a>
</body>

</html>