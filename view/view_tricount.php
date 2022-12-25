<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Operations</title>
</head>

<body>
<div class="main">
    <div class="title" id="t2">
        <a href="main/index/<?= $tricount->id ?>" class="button" id="back">Home</a>
        Expenses
        <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button" id="add">Edit</a>
    </div>
    <table>
        <?php foreach ($list as $operation) { ?>
            <tr>
                <td>
                    <p><b><a href="operation/details/<?= $operation->id ?>"><?=$operation->title?></a></b></p>
                    <p>Paid by <?= $operation->initiator->full_name ?></p>
                </td>
                <td>
                    <p><?= number_format($operation->amount, 2) ?>€</p>
                    <p><?= date("d/m/Y", strtotime($operation->operation_date)) ?></p>
                </td>
            </tr>
        <?php } ?>
    </table>
    <a href="operation/add_operation/<?= $tricount->id ?>" class="button" id="add">Add</a>
    <footer>
        <p>MY TOTAL<br><b>A NUM</b></p>
        <p>TOTAL EXPENSES<br><b>A NUM</b></p>
    </footer>
</div>
</body>

</html>