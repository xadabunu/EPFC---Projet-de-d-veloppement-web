<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <base href="<?= $web_root ?>">
    <title>Your Tricounts</title>
    <base href="<?= $web_root ?>" />
</head>

<body>
    <div class="title">Your Tricounts</div>
    <ul>
        <?php foreach ($data as $tricount) { ?>
            <li class="tricount">
                <div>
                    <a href="tricount/operations/<?= $tricount->id ?>"><p class="title"><?= $tricount->title ?></p></a>
                    <?php
                    $id = $tricount->id;
                    $n = $subs_number[$id];
                    if ($n > 0) { ?>
                        <p class="participants_number">
                            <?php echo "with $n friend(s)" ?></p>
                    <?php } ?>
                    <?php if($tricount -> description != 'NULL'){?>
                        <p><?= $tricount->description ?></p>
                    <?php } ?>    
                </div>
            </li>
        <?php } ?>
    </ul>
    <a href="main/logout">Logout</a>
</body>

</html>