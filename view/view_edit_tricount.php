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
                <td><?=$creator->full_name ?></td>
            </tr>
            <?php foreach ($subscriptors as $subscriptor) { ?>
                <tr>
                    <td> <?= $subscriptor->full_name ?></td>
            <?php } ?>
                </tr>
        </table>
        <form id= "cbo">
            <select name="subs" id="subs">
                <option value ="">--Add a new subscriber--</option>
                <?php foreach($cbo_users as $cbo_user) { ?>
                    <option value="<?=$cbo_user->full_name?>"><?=$cbo_user->full_name?></option>
                <?php } ?> 
            </select>
            <input type= "submit" value= "Add" formaction="tricount/add_subscriptors">
        </form>    
        <?php if (count($errors) != 0) : ?>
                <div class='errors'>
                    <br><br>
                    <p>Please correct the following error(s) : </p>
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?=  $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
    </div>
</body>

</html>