<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= $tricount->title ?> &#11208; Edit</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= strlen($title) > 20 ? substr($title, 0, 20)."..." : $title ?> &#11208; Edit</p>
            <button form="edittricountform" type="submit" class="button save" id="add">Save</button>
        </header>
        <h3>Settings</h3>
        <form id="edittricountform" action="tricount/edit_tricount/<?= $tricount->id ?>" method="post" class="edit">
            <label>Title :</label>
            <input id="title" name="title" type="text" value="<?= $tricount->title ?>" <?php if (array_key_exists('required', $errors) || array_key_exists('title_length', $errors) || array_key_exists('unique_title', $errors)) { ?>class="errorInput" <?php } ?>>
            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('title_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['title_length']; ?></p>
            <?php }
            if (array_key_exists('unique_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['unique_title']; ?></p>
            <?php } ?>
            <label>Description (optional) :</label>
            <textarea id="description" name="description" rows="3" placeholder="Description" <?php if (array_key_exists('description_length', $errors)) { ?>class="errorInput" <?php } ?>><?= $tricount->description ?></textarea>
            <?php if (array_key_exists('description_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['description_length']; ?></p>
            <?php } ?>
        </form>
        <h3>Subscriptions</h3>
        <table class="subs">
            <tr>
                <td class="subs"><?= strlen($creator->full_name) > 30 ? substr($creator->full_name, 0, 30)."..." : $creator->full_name ?> (creator)</td>
                <td></td>
            </tr>
            <?php foreach ($subscriptors as $subscriptor) { ?>
                <tr class="pop">
                    <td><?= strlen($subscriptor->full_name) > 30 ? substr($subscriptor->full_name, 0, 30)."..." : $subscriptor->full_name ?></td>
                    <td class="link">
                        <?php if (in_array($subscriptor, $deletables)) { ?>
                            <form id="delete_sub" class="link" action='tricount/delete_subscriptor/<?= $tricount->id ?>' method='post'>
                                <input type='text' name='subscriptor_name' value='<?= $subscriptor->id ?>' hidden>
                                <button type="submit" class="pop x"><i class="fa-regular fa-trash-can fa-sm" aria-hidden="true"></i></button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <form name="subscriptor" method="POST" class="edit">
            <table>
                <tr>
                    <td class="subscriptor">
                        <select name="subscriptor" id="subscriptor">
                            <option selected disabled>--Add a new subscriber--</option>
                            <?php foreach ($cbo_users as $cbo_user) { ?>
                                <option value="<?= $cbo_user->id ?>"><?= strlen($cbo_user->full_name) > 20 ? substr($cbo_user->full_name, 0, 20)."..." : $cbo_user->full_name ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="subscriptor input"><input type="submit" value="Add" formaction="tricount/add_subscriptors/<?= $tricount->id ?>"></td>
                </tr>
            </table>
        </form>
        <a href="templates/manage_templates/<?= $tricount->id ?>" class="button bottom2 manage">Manage repartition template</a>
        <a href="tricount/delete_tricount/<?= $tricount->id ?>" class="button bottom2 delete">Delete this tricount</a>
    </div>
</body>

</html>