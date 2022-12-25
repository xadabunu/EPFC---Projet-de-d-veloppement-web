<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Your Tricounts</title>
</head>

<body>
<div class="main">
    <header class="t2">
        <p>Your Tricounts</p>
    <a href="tricount/add_tricount" class="button" id="add">Add</a>
    </header>
    <ul>
        <?php foreach ($data as $tricount) { ?>
            <li class="tricount">
                <div>
                    <p class="title"><a href="tricount/operations/<?= $tricount->id ?>"><?= $tricount->title ?></a></p>
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
</div>

</body>

</html>