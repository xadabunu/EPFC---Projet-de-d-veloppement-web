<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= $tricount->title ?> &#11208; Templates</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= strlen($tricount->title) > 20 ? substr($tricount->title, 0, 20)."..." : $tricount->title ?> &#11208; Templates</p>
            <a href="templates/add_template/<?= $tricount->id ?>" class="button" id="add">Add</a>
        </header>
        <?php 
        $templates = RepartitionTemplates::get_all_repartition_templates_by_tricount_id($tricount->id);
        if (empty($templates)) { ?>
            <table>
                <tr>
                    <th class="empty">Currently no Templates !</th>
                </tr>
                <tr>
                    <td class="empty">
                        <p>Click below to add a Template!</p>
                        <a href="templates/add_template/<?= $tricount->id ?>" class="button">Add Template</a>
                    </td>
                </tr>
            </table>
        <?php } else { ?>
            <table>
                <?php
                    foreach($templates as $template) { ?>
                        <tr>
                            <td>
                                <a href="templates/edit_template/<?= $tricount->id ?>/<?= $template->id ?>">
                                    <h3><?= strlen($template->title) > 25 ? substr($template->title, 0, 25)."..." : $template->title ?></h3>
                                </a>
                                <ul>
                                    <?php
                                        $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($template->id);
                                        $poidsMax = 0;
                                        foreach($repartition_template_items as $item) {
                                            $poidsMax += $item->weight;
                                        }
                                        foreach($repartition_template_items as $item) {
                                            ?>
                                            <li class="listyle">
                                                <?= strlen($item->user->full_name . ' (' . $item->weight . '/' . $poidsMax . ')') > 30 ? substr($item->user->full_name, 0, 25)."... (" . $item->weight . '/' . $poidsMax . ')' : $item->user->full_name . ' (' . $item->weight . '/' . $poidsMax . ')' ?>
                                            </li>
                                            <?php
                                        } 
                                    ?>
                                </ul>
                            </td>
                        </tr>
                    <?php } 
                }?>
            </table>
    </div>
</body>

</html>