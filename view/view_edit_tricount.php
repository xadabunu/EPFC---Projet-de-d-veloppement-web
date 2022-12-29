<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <title>Edit_Tricount</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>

    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= $tricount->title ?> &#11208; Edit</p>
        </header>
        <form id="edittricountform" action="tricount/edit_tricount/<?= $tricount->id ?>" method="post">
            <input type="submit" value="Save" formaction="tricount/edit_tricount/<?= $tricount->id ?>">
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
        <table>
            <tr>
                <td><?= $creator->full_name ?>(Creator)</td>
            </tr>
            <?php foreach ($subscriptors as $subscriptor) { ?>
                <tr>
                    <form class="link" action='tricount/delete_subscriptor/<?= $tricount->id ?>' method='post'>
                        <input type='text' name='subscriptor_name' value='<?= $subscriptor->id ?>' hidden>
                        <td>
                            <p><?= $subscriptor->full_name ?><input type='submit' value='delete'>
                        </td>
                        </p>
                        </td>
                    </form>
                </tr>
            <?php } ?>
        </table>
        <form id="subscriptor" name="subscriptor" method="POST">
            
            <select name="subscriptor" id="subscriptor">
                <option value="">--Add a new subscriber--</option>
                <?php foreach ($cbo_users as $cbo_user) { ?>
                    <option value="<?= $cbo_user->id ?>"><?= $cbo_user->full_name ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="Add" formaction="tricount/add_subscriptors/<?= $tricount->id ?>">
        </form>
        <a href="tricount/delete_tricount/<?= $tricount->id ?>">Delete Tricount</a>
    </div>
</body>

</html>