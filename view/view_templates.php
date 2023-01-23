<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8">
    <title><?= $tricount->title ?> &#11208; Templates</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="main">
    <header class="t2">
            <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= $tricount->title ?> &#11208; Templates</p>
            <a href="templates/add_template/<?= $tricount->id ?>" class="button" id="add">Add</a>
    </header>

    <table>
        <?php 
        for($cpt = 0; $cpt != count($templates); $cpt++){ ?>
        <tr>
            <td>
                <a><h2><?= $templates[$cpt]->title ?></h2></a> <!-- Ajouter href vers edit template -->
                

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