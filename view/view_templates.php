<!DOCTYPE html>
<html lang="en">
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
            <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= $tricount->title ?> &#11208; Edit</p>
            <button form= "edittricountform" type="submit" class ="button save" id="save">Add</button>
    </header>

    <table>
        <?php foreach($templates as $template){ ?>
        <tr>
            <td>
                <a><h3><?= $template->title ?></h3></a> <!-- Ajouter href vers edit template -->
                <br>
                <li>
                    
                </li>


            </td>
        </tr>
        <?php } ?>

    </table>

    </div>
</body>
</html>