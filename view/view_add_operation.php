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
        <div class="title" id="t2">
            <a href="tricount/operations/<?=$tricount->id?>" class="button" id="back">Cancel</a>
            Add Operation
        </div>
        <form id="add_operation_form" action="operation/add_operation/<?=$tricount->id?>" method="post">
            <div class="formtitle">Add Operation</div>
            <input id="title" name="title" type="text" size="16" placeholder="Title">
            <?php if (array_key_exists('required', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['required'];?></p>
            <?php } 
            if(array_key_exists('lenght', $errors)){?>
                <p class="errorMessage"><?php echo $errors['lenght'];?></p>
            <?php } ?>
            <input id="Amount" name="amount" type="text" size="16" placeholder="Amount">
            <?php if(array_key_exists('amount', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['amount'];?></p>
            <?php } ?> 
            Date
            <input id="operation_date" name="operation_date" type="date" required>
            <?php if(array_key_exists('date', $errors)){?>
                <p class="errorMessage"><?php echo $errors['date'];?></p>
            <?php } ?>
            Paid by
            <select name="paid_by" id="paid_by" required>
                <option value="">-- Who paid for it ? --</option>
                <?php foreach($subscriptors as $subscriptor) { ?>
                    <option value="<?= $subscriptor->id ?>"><?= $subscriptor->full_name ?></option>
                <?php } ?>
            </select>
            <?php if(array_key_exists('paid', $errors)){?>
                <p class="errorMessage"><?php echo $errors['paid'];?></p>
            <?php } ?>
            Use repartition template (Optional)
            <select name="templates" id="templates">
                <option value="">-- No, i'll use custom repartition --</option>
                <?php foreach($templates as $template) { ?>
                    <option value="<?= $template->id ?>"><?= $template->title ?></option>
                <?php } ?>
            </select>
            For whom ? (Select at leat one)
            <table>
                <?php foreach($subscriptors as $subscriptor){?>
                    <tr>
                        <td><input type="checkbox" id="repartitions" name="repartitions"></td>
                        <td><?=$subscriptor->full_name;?></td>
                        <td>weight</td>
                    </tr>
                <?php } ?>
            </table>
            Add a new repartition template
            <tabel>
                <tr>
                    <td><input type="checkbox" id="save_template" name="save_template"></td>
                    <td>Save this template</td> 
                    <td>name</td>
                </tr>
            </tabel>
            <input type="submit" value="Save" formaction="operation/add_operation/<?=$tricount->id?>">
        </form>
    </div>