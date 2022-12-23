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
            <li><a href="operation/details/<?= $operation->id ?>"><?=$operation->title?></a></li>
        <?php } ?>
    </ul>
    <a href="main/index/<?= $tricount->id ?>">Home</a>
    <a href="tricount/edit_tricount/<?= $tricount->id ?>">Edit</a>
        </div>
</body>

</html>