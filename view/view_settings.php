<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8">
    <title>Settings</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="main">
    <header class="t2">
            <a href="tricount" class="button" id="back">Back</a>
            <p>Settings</p>
            <p></p>
    </header>

    <table>
        <?php 
        for($cpt = 0; $cpt != count($templates); $cpt++){ ?>
        <tr>
            <td>
                <a href="templates/edit_template/<?= $tricount->id ?>/<?= $templates[$cpt]->id ?>"><h2><?= $templates[$cpt]->title ?></h2></a>

                <ul>
                    <?php 
                    
                    foreach($all_templates_items_for_view[$cpt] as $nom => $poids){ ?>

                        <li>
                            <?= $nom . ' (' . $poids . '/' . $all_weight_total[$cpt] . ')' ?>
                        </li>

                    <?php } ?>
                </ul>


            </td>
        </tr>
        <?php } ?>
    </table>       

    </div>
</body>
</html>