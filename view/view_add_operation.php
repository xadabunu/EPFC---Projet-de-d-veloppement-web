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
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Cancel</a>
            <p>Add Operation</p>
        </header>
        <form id="add_operation_form" action= "operation/add_operation/<?= $tricount->id ?>" method="post">
            <div class="formtitle">Add Operation</div>
            <input id="title" name="title" type="text" size="16" placeholder="Title">
            <?php if (array_key_exists('empty_title', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['empty_title'];?></p>
            <?php } 
            if(array_key_exists('lenght', $errors)){?>
                <p class="errorMessage"><?php echo $errors['lenght'];?></p>
            <?php } ?>
            <input id="Amount" name="amount" type="text" size="16" placeholder="Amount">
            <?php if(array_key_exists('amount', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['amount'];?></p>
            <?php }
            if(array_key_exists('empty_amount', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_amount'];?></p>
            <?php }
            if(array_key_exists('amount', $errors)){?>
                <p class="errorMessage"><?php echo $errors['amount'];?></p>
            <?php } ?>
            Date
            <input id="operation_date" name="operation_date" type="date" >
            <?php if(array_key_exists('empty_date', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_date'];?></p>
            <?php } ?>
            Paid by
            <select name="paid_by" id="paid_by" >
                <option value="">-- Who paid for it ? --</option>
                <?php foreach($subscriptors as $subscriptor) { ?>
                    <option value="<?= $subscriptor->id ?>"><?= $subscriptor->full_name ?></option>
                <?php } ?>
            </select>
            <?php if(array_key_exists('empty_initiator', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_initiator'];?></p>
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
                        <td>
                            <p><input type='checkbox' name='<?= $subscriptor->id ?>' value=''>
                        </td>
                        <td><?= $subscriptor->full_name ?></td>
                        </p>
                        </td>
                        <td>weight<input type= 'text' name= 'weight_<?= $subscriptor->id ?>'></td>
                    </tr>
                <?php } ?>
            </table>
            <?php if(array_key_exists('whom', $errors)){?>
                <p class="errorMessage"><?php echo $errors['whom'];?></p>
            <?php } ?>
            Add a new repartition template
            <table>
                <tr>
                    <td><input type="checkbox" id="save_template" name="save_template"></td>
                    <td>Save this template</td> 
                    <td>name</td>
                </tr>
            </table>
            <input type="submit" value="Save" >
        </form>
    </div>