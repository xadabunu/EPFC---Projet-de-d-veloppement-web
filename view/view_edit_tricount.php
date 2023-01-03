<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <title><?= $tricount->title ?> &#11208; Edit</title>
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
        <h3>Settings</h3>
        <form id="edittricountform" action="tricount/edit_tricount/<?= $tricount->id ?>" method="post" class="edit">
            <input type="submit" value="Save" formaction="tricount/edit_tricount/<?= $tricount->id ?>" class="button save">
            <label>Title :</label>
            <input id="title" name="title" type="text" value="<?= $tricount->title ?>">
            <label>Description (optional) :</label>
            <input id="description" name="description" type="textarea" value="<?= $tricount->description ?>"></td>
        </form>
        <h3>Subscriptions</h3>
        <table class="subs">
            <tr>
                <td class="subs"><?= $creator->full_name ?> (creator)</td>
            </tr>
            <?php foreach ($subscriptors as $subscriptor) { ?>
                <tr class="pop">
                    <td><?= $subscriptor->full_name ?></td>
                    <td class="link">
                        <form class="link" action='tricount/delete_subscriptor/<?= $tricount->id ?>' method='post'>
                            <input type='text' name='subscriptor_name' value='<?= $subscriptor->id ?>' hidden>
                            <input type='submit' value='delete' class="pop">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <form id="subscriptor" name="subscriptor" method="POST">
            <table>
                <td><select name="subscriptor" id="subscriptor">
                <option value="">--Add a new subscriber--</option>
                <?php foreach ($cbo_users as $cbo_user) { ?>
                    <option value="<?= $cbo_user->id ?>"><?= $cbo_user->full_name ?></option>
                <?php } ?>
            </select></td>
                <td><input type="submit" value="Add" formaction="tricount/add_subscriptors/<?= $tricount->id ?>"></td>
            </table>
        </form>
        <a href="tricount/delete_tricount/<?= $tricount->id ?>" class="button bottom2 delete">Delete this tricount</a>
    </div>
</body>

</html>