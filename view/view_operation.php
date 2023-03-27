<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $operation->tricount->title ?> &#11208; <?= $operation->title ?></title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $operation->tricount->id ?>" class="button" id="back">Back</a>
            <p><?= strlen($operation->tricount->title) > 25 ? substr($operation->tricount->title, 0, 20)."..." : $operation->tricount->title ?> &#11208; <?= strlen($operation->title) > 25 ? substr($operation->title, 0, 20)."..." : $operation->title ?></p>
            <a href="operation/edit_operation/<?= $operation->id ?>" class="button" id="add">Edit</a>
        </header>
        <div>
            <div class="amount"><?= number_format($operation->amount, 2) ?> €</div>
            <div class="payement_info">
                <p>Paid by <?= strlen($operation->initiator->full_name) > 20 ? substr($operation->initiator->full_name, 0, 20)."..." : $operation->initiator->full_name ?></p>
                <p><?= date("d/m/Y", strtotime($operation->operation_date)) ?></p>
            </div>
        </div>
        <div>
            <p>For <?= count($list) ?> participants<?php if (in_array($user, $list)) {
                                                        echo " including <b>me</b>";
                                                    } ?></p>
            <table class="participants">
                <?php foreach ($list as $participant) : ?>
                    <tr>
                        <td><?= strlen($participant->full_name) > 20 ? substr($participant->full_name, 0, 20)."..." : $participant->full_name ?></td>
                        <td class="right"><?= round($amounts[$participant->id], 2) ?> €</td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
        <footer>
            <?php if ($previous != NULL) { ?>
                <a href="operation/details/<?= $previous ?>" class="button" id="previous">&#129060;</a>
            <?php } ?>
            <?php if ($next != NULL) { ?>
                <a href="operation/details/<?= $next ?>" class="button" id="next">&#129062;</a>
            <?php } ?>
        </footer>
    </div>
</body>

</html>