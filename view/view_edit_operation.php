<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Edit Operation</title>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="operation/details/<?=$operation->id?>" class="button" id="back">Cancel</a>
            <p>Edit Operation</p>
        </header>
        <form id="edit_operation_form" action="operation/edit_operation/<?=$operation->id?>" method="post">
            <div class="formtitle">Edit Operation</div>
            <input id="title" name="title" type="text" size="16" placeholder="Title" value= "<?=$operation->title?>" <?php if(array_key_exists('empty_title', $errors) || array_key_exists('lenght', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if (array_key_exists('empty_title', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['empty_title'];?></p>
            <?php } 
            if(array_key_exists('lenght', $errors)){?>
                <p class="errorMessage"><?php echo $errors['lenght'];?></p>
            <?php } ?>
            <input id="Amount" name="amount" type="text" size="16" placeholder="Amount" value= "<?=$operation->amount?>" <?php if(array_key_exists('amount', $errors) || array_key_exists('empty_amount', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if(array_key_exists('amount', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['amount'];?></p>
            <?php }
            if(array_key_exists('empty_amount', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_amount'];?></p>
            <?php } ?>
            Date
            <input id="operation_date" name="operation_date" type="date" value = "<?=$operation->operation_date?>" <?php if(array_key_exists('empty_date', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if(array_key_exists('empty_date', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_date'];?></p>
            <?php } ?>
            Paid by
            <select name="paid_by" id="paid_by" >
                <option value="<?= $operation->initiator->id ?>" ><?= $operation->initiator->full_name ?></option>
                <?php foreach($subscriptors as $subscriptor) { ?>
                    <option value="<?= $subscriptor->id ?>"><?= $subscriptor->full_name ?></option>
                <?php } ?>
            </select>
            <?php if(array_key_exists('empty_initiator', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_initiator'];?></p>
            <?php } ?>
            Use repartition template (Optional)
            <select name="templates" id="templates">
                <option value= ""></option>
                <?php foreach($templates as $template) { ?>
                    <option value="<?= $template->id ?>"><?= $template->title ?></option>
                <?php } ?>
            </select>
            For whom ? (Select at leat one)
            <table>
                <?php foreach($subscriptors as $subscriptor){?>
                    <tr>
                        <td>
                            <p><input type='checkbox' <?php if (array_key_exists($subscriptor->id, $list)) echo "checked"?> name='<?= $subscriptor->id ?>' value=''>
                        </td>
                        <td><?= $subscriptor->full_name ?></td>
                        </p>
                        </td>
                        <td>weight<input type= 'text' name= 'weight_<?= $subscriptor->id ?>' value= '<?php if(array_key_exists($subscriptor->id, $list)) echo $list[$subscriptor->id] ?>'></td>
                    </tr>
                <?php } ?>
            </table>
            Add a new repartition template
            <table>
                <tr>
                    <td><input type="checkbox" id="save_template" name="save_template"></td>
                    <td>Save this template</td> 
                    <td>name</td>
                </tr>
            </table>
            <?php if(array_key_exists('whom', $errors)){?>
                <p class="errorMessage"><?php echo $errors['whom'];?></p>
            <?php } ?>
            <input type="submit" value="Save" formaction="operation/edit_operation/<?=$operation->id?>">
            <a href="operation/delete_operation/<?= $operation->id ?>">Delete operation</a>
        </form>
    </div>