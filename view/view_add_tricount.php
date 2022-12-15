<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <base href="<?= $web_root ?>">
    <title>Add Tricount</title>
</head>

<body>
    <div class="title">Add Tricount</div>
    <div class="menu">
        <a href="index.php">Cancel</a>
    </div>
    <div class="main">
        <form id="add_tricount" action="tricount/add_tricount" method="post">
            <table>
                <tr>
                    <td><input id="title" name="title" type="text" size="16" placeholder="Title"></td>
                </tr>
                <tr>
                    <td><input id="description" name="description" type="text" size="16" placeholder="Description"></td>
                </tr>
            </table>
            <input type="submit" value="Add Tricount">
        </form>
</body>