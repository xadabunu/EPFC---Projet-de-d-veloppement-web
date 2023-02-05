<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title><?= $tricount->title ?> &#11208; Expenses</title>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="main/index/<?= $tricount->id ?>" class="button" id="back">Home</a>
            <p><?= strlen($tricount->title) <= 20 ? $tricount->title : substr($tricount->title, 0, 18)."..." ?> &#11208; Expenses</p>
            <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button" id="add">Edit</a>
        </header>
        <?php if (empty($list)) { ?>
            <?php if ($alone) { ?>
                <table>
                    <tr>
                        <th class="empty">You are alone!</th>
                    </tr>
                    <tr>
                        <td class="empty">
                            <p>Click below to add your friends!</p>
                            <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button">Add Friends</a>
                        </td>
                    </tr>
                </table>
            <?php } else { ?>
                <table>
                    <tr>
                        <th class="empty">Your tricount is empty!</th>
                    </tr>
                    <tr>
                        <td class="empty">
                            <p>Click below to add your first expense!</p>
                            <a href="operation/add_operation/<?= $tricount->id ?>" class="button">Add an expense</a>
                        </td>
                    </tr>
                </table>
            <?php }
        } else { ?>
            <p class="balance"><a href="tricount/balance/<?= $tricount->id ?>" class="button" id="balance"><b>&#8644;</b> View balance</a></p>
            <table>
                <?php foreach ($list as $operation) { ?>
                    <tr>
                        <td>
                            <p><b><a href="operation/details/<?= $operation->id ?>"><?= strlen($operation->title) > 35 ? substr($operation->title, 0, 30)."..." : $operation->title ?></a></b></p>
                            <p>Paid by <?= strlen($operation->initiator->full_name) > 25 ? substr($operation->initiator->full_name, 0, 23) : $operation->initiator->full_name ?></p>
                        </td>
                        <td class="right">
                            <p><b><?= round($operation->amount, 2) ?> €</b></p>
                            <p><?= date("d/m/Y", strtotime($operation->operation_date)) ?></p>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
        <footer>
            <div>
                <p>MY TOTAL</p>
                <p><b><?= number_format($user_total, 2) ?> €</b></p>
            </div>
            <a href="operation/add_operation/<?= $tricount->id ?>" class="circle">+</a>
            <div>
                <p>TOTAL EXPENSES</p>
                <p><b><?= number_format($total, 2) ?> €</b></p>
            </div>
        </footer>
    </div>
</body>

</html>