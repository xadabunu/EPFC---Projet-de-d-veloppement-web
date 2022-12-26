<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title><?= $tricount->title ?> > Expenses</title>
</head>

<body>
<div class="main">
    <header class="t2">
        <a href="main/index/<?= $tricount->id ?>" class="button" id="back">Home</a>
        <p><?= $tricount->title ?> &#11208; Expenses</p>
        <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button" id="add">Edit</a>
    </header>
    <table>
        <?php foreach ($list as $operation) { ?>
            <tr>
                <td>
                    <p><b><a href="operation/details/<?= $operation->id ?>"><?=$operation->title?></a></b></p>
                    <p>Paid by <?= $operation->initiator->full_name ?></p>
                </td>
                <td>
                    <p><b><?= round($operation->amount, 2) ?>€</b></p>
                    <p><?= date("d/m/Y", strtotime($operation->operation_date)) ?></p>
                </td>
            </tr>
        <?php } ?>
    </table>
    <footer>
        <div><p>MY TOTAL</p><p><b><?= number_format($user_total, 2) ?>€</b></p></div>
        <a href="operation/add_operation/<?= $tricount->id ?>" class="circle">+</a>
        <div><p>TOTAL EXPENSES</p><p><b><?= number_format($total, 2) ?>€</b></p></div>
    </footer>
</div>
</body>
</html>