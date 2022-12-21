<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Add Operation</title>
</head>

<body>
    <div class="main">
        <div class="title" id="t2">Add Operation</div>
        <div class="menu">    
            <a href="tricount/operations/<?=$tricount->id?>">Cancel</a>
        </div>
        <form id="add_operation_form" action="operation/add_operation" method="post">
            <div class="formtitle">Add Operation</div>
            <input id="title" name="title" type="text" size="16" placeholder="Title"> 
            <input id="Amount" name="Amount" type="text" size="16" placeholder="Amount"> 
            Date
            <input id="operation_date" name="operation_date" type="date">
            Paid by
            <select name="paid_by" id="paid_by">
                <option value="">-- Who paid for it ? --</option>
                <?php foreach($subscriptors as $subscriptor) { ?>
                    <option value="<?= $subscriptor->id ?>"><?= $subscriptor->full_name ?></option>
                <?php } ?>
            </select>
            Use repartition template (Optional)
            <select name="templates" id="templates">
                <option value="">-- No, i'll use custom repartition --</option>
                <?php foreach($templates as $template) { ?>
                    <option value="<?= $template->id ?>"><?= $template->title ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="Save">
        </form>
    </div>