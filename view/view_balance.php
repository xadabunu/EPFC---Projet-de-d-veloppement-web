<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <title><?= $tricount->title ?> &#11208; balance</title>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= $tricount->title ?> &#11208; balance</p>
            <p></p>
        </header>
        <table class="balance">
            <?php foreach ($subs as $sub) { ?>
                <tr class="balance">
                    <?php if ($amounts[$sub->id] >= 0) { ?>
                        <td class="balance">
                            <p class="left<?php if ($sub->id === $user->id) {
                                                echo " bold";
                                            } ?>"><?= $sub->full_name ?><?php if ($sub->id === $user->id) {
                                                                                                                            echo " (me)";
                                                                                                                        } ?></p>
                        </td>
                        <td class="positive balance">
                            <p class="<?php if ($amounts[$sub->id] != 0) {echo "positive";} ?> right<?php if ($sub->id === $user->id) {echo " bold";} ?>" style="width: <?= abs($amounts[$sub->id] / $max * 100) ?>%;">
                            <p class="inner"><?= round($amounts[$sub->id], 2) ?> €</p>
                        </td>
                    <?php } else { ?>
                        <td class="negative balance">
                            <p class="negative left" style="width: <?= abs($amounts[$sub->id] / $max * 100) ?>%;">
                            <p class="inner left<?php if ($sub->id === $user->id) {echo " bold";} ?>"><?= round($amounts[$sub->id], 2) ?> €</p>
                        </td>
                        <td class="balance">
                            <p class="right<?php if ($sub->id === $user->id) {echo " bold";} ?>"><?= $sub->full_name ?><?php if ($sub->id === $user->id) {echo " (me)";} ?></p>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>

</body>

</html>