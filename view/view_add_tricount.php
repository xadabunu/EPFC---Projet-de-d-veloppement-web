<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Add Tricount</title>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="main/index" class="button" id="back">Cancel</a>
            <p>Add Tricount</p>
            <button class="button save" id="add" type="submit" form="add_tricount">Save</button>
        </header>
        <div class="menu">

        </div>
        <form class="edit" id="add_tricount" action="tricount/add_tricount" method="post">
            <h3>Titre</h3>
            <input id="title" name="title" type="text" value="<?= $title ?>" placeholder="Title" <?php if (array_key_exists('required', $errors) || array_key_exists('title_length', $errors) || array_key_exists('unique_title', $errors)) { ?>class="errorInput" <?php } ?>>
            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('title_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['title_length']; ?></p>
            <?php }
            if (array_key_exists('unique_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['unique_title']; ?></p>
            <?php } ?>
            <h3>Description (Optional) :</h3>
            <textarea id="description" name="description" rows="6" placeholder="Description" <?php if (array_key_exists('description_length', $errors)) { ?>class="errorInput" <?php } ?>></textarea>
            <?php if (array_key_exists('description_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['description_length']; ?></p>
            <?php } ?>
        </form>
    </div>
</body>