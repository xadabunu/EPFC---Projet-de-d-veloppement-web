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
        <div class="title" id="t2">Add Tricount</div>
        <div class="menu">
            <a href="index.php">Cancel</a>
        </div>
        <form id="add_tricount" action="tricount/add_tricount" method="post">
            <table>
                <tr>
                    <td><input id="title" name="title" type="text" size="16" placeholder="Title"></td>
                </tr>
                <tr>
                    <td><input id="description" name="description" type="text" size="16" placeholder="Description"></td>
                </tr>
            </table>
            <input type="submit" value="Add Tricount">
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
</body>