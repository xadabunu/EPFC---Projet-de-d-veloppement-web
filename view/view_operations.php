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
    <ul>
        <?php foreach ($list as $operation) { ?>
            <li> <?=$operation->title?></li>
        <?php } ?>
    </ul>
    <a href="main/index/<?= $tricount->id ?>">Home</a>
    <a href="tricount/edit_tricount/<?= $tricount->id ?>">Edit</a>
</body>
</html>