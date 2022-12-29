<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <title>Delete Operation</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>

    <div class="main">
        <header class="t2"><p> Are your sure ? </p></header>
        <p>Do you really want to delete operation <?= $operation->title ?> and all of his dependencies ? </p>
        <p>This process can't be undone.</p>
        <form>
            <input type="submit" value="Delete" formaction="operation/confirm_delete_operation/<?= $operation->id ?>">
            <div class="menu"><a href="operation/edit_operation/<?= $operation->id ?>">Cancel</a></div>
        </form>
    </div>
</body>