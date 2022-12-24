<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $tricount->title ?> > <?= $operation->title ?></title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="main">
        <div class="title" id="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <?= "$tricount->title > $operation->title" ?>
        </div>
        <header>
            <div class="amount"><?php echo number_format($operation->amount, 2) ?> €</div>
            <div class="payement_info">
                <p>Paid by <?= $initiator->full_name ?></p>
                <p><?= $operation->operation_date ?></p>
            </div>
        </header>
        <div>
            <p>For <?= count($list) ?> participants<?php if (in_array($user, $list)) {
                                                        echo " including <b>me</b>";
                                                    } ?></p>
            <table class="participants">
                <?php foreach ($list as $participant) : ?>
                    <tr>
                        <td><?= $participant->full_name ?></td>
                        <td><?= number_format($amounts[$participant->id], 2) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
        <div class="footer">
            <?php if ($previous != NULL) { ?>
                <a href="operation/details/<?= $previous ?>" class="button" id="previous">Previous</a>
            <?php } ?>
            <?php if ($next != NULL) { ?>
                <a href="operation/details/<?= $next ?>" class="button" id="next">Next</a>
            <?php } ?>
        </div>
    </div>
</body>
</html>