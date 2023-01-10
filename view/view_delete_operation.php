<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset="utf-8">
    <title>Delete Operation</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

    <div class="main">
    <table class = "confirm_delete">
            <tr>
                <td class="empty"><i class="fa fa-trash-o fa-3x" aria-hidden="true"></i><p id="confirm_title" class = "text_confirm_title">Are you sure?</p></td>
            </tr>
            <tr>
                <td class="empty">
                    <p class = "text_confirm">Do you really want to delete operation <b>"<?= $operation->title ?>"</b> and all of his dependencies ? <br> This process can't be undone.</p>
                    <a href="operation/edit_operation/<?= $operation->id ?>" class="button btn_cancel">Cancel</a>
                    <a href="operation/confirm_delete_operation/<?= $operation->id ?>" class="button btn_delete">Delete</a>
                </td>
            </tr>
        </table>
    </div>
</body>