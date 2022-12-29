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
        </header>
        <div class="menu">

        </div>
        <form id="add_tricount" action="tricount/add_tricount" method="post">
            <input id="title" name="title" type="text" size="16" value="<?= $title ?>" placeholder="Title">
            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('title_lenght', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['title_lenght']; ?></p>
            <?php } ?>

            <input id="description" name="description" type="text" size="16" placeholder="Description">
            <?php if (array_key_exists('description_lenght', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['description_lenght']; ?></p>
            <?php } ?>

            <input type="submit" value="Add Tricount">
        </form>
    </div>
</body>