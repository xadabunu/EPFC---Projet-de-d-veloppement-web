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
    <div class="title" id="t2">Expenses</div>
    <ul>
        <?php foreach ($list as $operation) { ?>
            <li><a href="operation/details"><?=$operation->title?></a></li>
        <?php } ?>
    </ul>
    <a href="main/index/<?= $tricount->id ?>">Home</a>
    <a href="tricount/edit_tricount/<?= $tricount->id ?>">Edit</a>
    <a href="operation/add_operation/<?= $tricount->id ?>">Add</a>
    <a href ="operation/edit_operation/4">Edit operation TEMP TEST</a>
        </div>
</body>

</html>