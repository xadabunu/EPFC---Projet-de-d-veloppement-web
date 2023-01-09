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
            <button form= "edittricountform" type="submit" class ="button save" id="add">Save</button>
        </header>
        <h3>Settings</h3>
        <form id="edittricountform" action="tricount/edit_tricount/<?= $tricount->id ?>" method="post" class="edit">
            <!-- <input type="submit" value="Save" formaction="tricount/edit_tricount/<?= $tricount->id ?>" class="button save" id= "add"> -->
            <label>Title :</label>
            <input id="title" name="title" type="text" value="<?= $tricount->title ?>" <?php if(array_key_exists('required', $errors) || array_key_exists('title_lenght', $errors) || array_key_exists('unique_title', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('title_lenght', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['title_lenght']; ?></p>
            <?php }
            if (array_key_exists('unique_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['unique_title']; ?></p>
            <?php } ?>
            <label>Description (optional) :</label>
            <textarea id="description" name="description" rows="3" placeholder="Description" <?php if(array_key_exists('description_lenght', $errors)) {?>class = "errorInput"<?php } ?>><?= $tricount->description ?></textarea>
            <?php if (array_key_exists('description_lenght', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['description_lenght']; ?></p>
            <?php } ?>
        </form>
        <h3>Subscriptions</h3>
        <table class="subs">
            <tr>
                <td class="subs"><?= $creator->full_name ?> (creator)</td>
                <td></td>
            </tr>
            <?php foreach ($subscriptors as $subscriptor) { ?>
                <tr class="pop">
                    <td><?= $subscriptor->full_name ?></td>
                    <td class="link">
                        <form id="delete_sub" class="link" action='tricount/delete_subscriptor/<?= $tricount->id ?>' method='post'>
                            <input type='text' name='subscriptor_name' value='<?= $subscriptor->id ?>' hidden>
                            <button type="submit" class="pop x">&#215;</i></button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <form id="subscriptor" name="subscriptor" method="POST" class="edit">
            <table>
                <td class="subscriptor">
                    <select name="subscriptor" id="subscriptor">
                        <option selected disabled>--Add a new subscriber--</option>
                        <?php foreach ($cbo_users as $cbo_user) { ?>
                            <option value="<?= $cbo_user->id ?>"><?= $cbo_user->full_name ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td class="subscriptor input"><input type="submit" value="Add" formaction="tricount/add_subscriptors/<?= $tricount->id ?>"></td>
            </table>
        </form>
        <a href="templates/manage_templates/<?= $tricount->id ?>" class="button bottom2 manage">Manage repartition template</a>
        <a href="tricount/delete_tricount/<?= $tricount->id ?>" class="button bottom2 delete">Delete this tricount</a>
    </div>
</body>

</html>