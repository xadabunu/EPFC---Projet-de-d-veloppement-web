<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $operation->tricount->title ?> > <?= $operation->title ?></title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href ="operation/edit_operation/<?= $operation->id ?>" class= "button" id= "add">Edit</a>
            <a href="tricount/operations/<?= $operation->tricount->id ?>" class="button" id="back">Back</a>
            <p><?php echo $operation->tricount->title ?> &#11208; <?= $operation->title ?></p>
        </header>
        <div>
            <div class="amount"><?php echo number_format($operation->amount, 2) ?> €</div>
            <div class="payement_info">
                <p>Paid by <?= $operation->initiator->full_name ?></p>
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
                        <td><?= $participant->full_name ?></td>
                        <td><?= round($amounts[$participant->id], 2) ?>€</td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
        <footer>
            <?php if ($previous != NULL) { ?>
                <a href="operation/details/<?= $previous ?>" class="button" id="previous">Previous</a>
            <?php } ?>
            <?php if ($next != NULL) { ?>
                <a href="operation/details/<?= $next ?>" class="button" id="next">Next</a>
            <?php } ?>
        </footer>
    </div>
</body>
</html>