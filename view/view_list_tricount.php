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
        <table>
            <?php foreach ($data as $tricount) { ?>
                <tr>
                    <td>
                        <p><b><a href="tricount/operations/<?= $tricount->id ?>"><?= $tricount->title ?></a></b></p>
                        <p><?= $tricount->description ?></p>
                    </td>
                    <td>
                        <p><?= $subs_number[$tricount->id] ?></p>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <a href="main/logout">Logout</a>
    </div>

</body>

</html>