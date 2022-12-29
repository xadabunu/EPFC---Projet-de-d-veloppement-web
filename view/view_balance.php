<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title><?= $tricount->title ?> > balance</title>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= $tricount->title ?> &#11208; balance</p>
        </header>
        <table class="balance">
            <?php foreach ($subs as $sub) { ?>
                <tr>
                    <?php if ($amounts[$sub->id] >= 0) { ?>
                        <td class="left">
                            <?php if ($sub->id === $user->id) {
                                echo "<b>";
                            } ?>
                            <?= $sub->full_name ?>
                            <?php if ($sub->id === $user->id) {
                                echo "(me)</b>";
                            } ?>
                        </td>
                        <td class="positive right" style="width: <?= abs($amounts[$sub->id] / $max * 50) ?>%;">
                            <?php if ($sub->id === $user->id) {
                                echo "<b>";
                            } ?>
                            <?= round($amounts[$sub->id], 2) ?> €
                            <?php if ($sub->id === $user->id) {
                                echo "</b>";
                            } ?>
                        </td>
                    <?php } else { ?>
                        <td class="negative left" style="width: <?= $amounts[$sub->id] / $max * 50 ?>%;">
                            <?php if ($sub->id === $user->id) {
                                echo "<b>";
                            } ?>
                            <?= round($amounts[$sub->id], 2) ?> €
                            <?php if ($sub->id === $user->id) {
                                echo "</b>";
                            } ?>
                        </td>
                        <td class="right">
                            <?php if ($sub->id === $user->id) {
                                echo "<b>";
                            } ?>
                            <?= $sub->full_name ?>
                            <?php if ($sub->id === $user->id) {
                                echo "(me)</b>";
                            } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <?php print_r(array_map("abs", $amounts)) ?>
    </div>

</body>

</html>