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
        <table class = "confirm_delete">
            <tr>
                <td class="empty"><p class = "text_confirm_title">Are you sure?</p></td>
            </tr>
            <tr>
                <td class="empty">
                    <p class = "text_confirm">Do you really want to delete tricount <b>"<?= $tricount->title ?>"</b> and all of his dependencies ? <br> This process can't be undone.</p>
                    <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button btn_cancel">Cancel</a>
                    <a href="tricount/confirm_delete_tricount/<?= $tricount->id ?>" class="button btn_delete">Delete</a>
                </td>
            </tr>
        </table>
    </div>
</body>