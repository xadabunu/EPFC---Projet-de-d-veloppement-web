<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <title>Edit_Tricount</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css" />
</head>

<body>
    <div class="title">Edit</div>
    <div class="menu">
        <a href="tricount/operations/<?= $tricount->id ?>">Back</a>
    </div>
    <div class="main">
        <form id="edittricountform" action="tricount/edit_title_description/<?= $tricount->id ?>" method="post">
        <input type="submit" value="Save" formaction="tricount/edit_title_description/<?= $tricount->id ?>">
            <table>
                <h3>Settings</h3>
                <tr>
                    <td>Title</td>
                </tr>
                <tr>
                    <td><input id="title" name="title" type="text" size="16" value="<?= $tricount->title ?>"></td>
                </tr>
                <tr>
                    <td>Description (Optional)</td>
                </tr>
                <tr>
                    <td><input id="description" name="description" type="text" size="16" value="<?= $tricount->description ?>"></td>
                </tr>
            </table>
        </form>
        <h3>Subscriptions</h3>
        
        <ul>
            <li><?=$creator->full_name ?></li>
            <?php foreach ($subscriptors as $subscriptor) { ?>
                <li> <?= $subscriptor->full_name ?></li>
            <?php } ?>
        </ul>
    </div>
</body>

</html>