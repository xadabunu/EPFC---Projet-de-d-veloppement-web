<!DOCTYPE html>
<html>

<head>
    <meta charset="utf8">
    <title>Edit_Tricount</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body>

    <div class="main">
        <div class="title" id="t2"> Are your sure ? </div>
        <p>Do you really want to delete tricount <?= $tricount->title ?> and all of his dependencies ? </p>
        <p>This process can't be undone.</p>
        <form>
            <input type="submit" value="Delete" formaction="tricount/confirm_delete_tricount/<?= $tricount->id ?>">
            <div class="menu"><a href="tricount/edit_tricount/<?= $tricount->id ?>">Cancel</a></div>
        </form>
        
