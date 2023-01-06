<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title><?= $operation->title ?> &#11208; Edit</title>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="operation/details/<?=$operation->id?>" class="button" id="back">Cancel</a>
            <p><?= $operation->title ?> &#11208; Edit</p>
        </header>
        <form id="edit_operation_form" action="operation/edit_operation/<?=$operation->id?>" method="post" class="edit">
            <input id="title" name="title" type="text" size="16" placeholder="Title" value= "<?=$operation->title?>" <?php if(array_key_exists('empty_title', $errors) || array_key_exists('lenght', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if (array_key_exists('empty_title', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['empty_title'];?></p>
            <?php } 
            if (array_key_exists('lenght', $errors)) {?>
                <p class="errorMessage"><?php echo $errors['lenght'];?></p>
            <?php } ?>
            <table class="edit" id="currency">
                <tr class="currency">
                    <td><input id="amount" name="amount" type="text" size="16" placeholder="Amount" value= "<?=$operation->amount?>" <?php if(array_key_exists('amount', $errors) || array_key_exists('empty_amount', $errors)) {?>class = "errorInput"<?php } ?>></td>
                    <td class="right">EUR</td>
                </tr>
            </table>
            <?php if(array_key_exists('amount', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['amount'];?></p>
            <?php }
            if(array_key_exists('empty_amount', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_amount'];?></p>
            <?php } ?>
            <label for="operation_date">Date</label>
            <input id="operation_date" name="operation_date" type="date" value = "<?=$operation->operation_date?>" <?php if(array_key_exists('empty_date', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if(array_key_exists('empty_date', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_date'];?></p>
            <?php } ?>
            <label for="paid_by">Paid by</label>
            <select name="paid_by" id="paid_by" class="edit edit2">
                <option value="<?= $operation->initiator->id ?>" ><?= $operation->initiator->full_name ?></option>
                <?php foreach($subscriptors as $subscriptor) { ?>
                    <option value="<?= $subscriptor->id ?>"><?= $subscriptor->full_name ?></option>
                <?php } ?>
            </select>
            <?php if(array_key_exists('empty_initiator', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_initiator'];?></p>
            <?php } ?>
            <label for="templates">Use repartition template <i>(optional)</i></label>
            <select name="templates" id="templates" class="edit edit2">
                <option selected><i>-- No, i'll use custom repartition --</i></option>
                <?php foreach($templates as $template) { ?>
                    <option value="<?= $template->id ?>"><?= $template->title ?></option>
                <?php } ?>
            </select>
            <label for="whom">For whom ? <i>(select at leat one)</i></label>
            <ul>
                <?php foreach($subscriptors as $subscriptor){?>
                    <li>
                    <table class="whom">
                    <tr class="edit">
                        <td class="check">
                            <p><input type='checkbox' <?php if (array_key_exists($subscriptor->id, $list)) echo "checked"?> name='<?= $subscriptor->id ?>' value=''></p>
                        </td>
                        <td class="user">
                            <?= $subscriptor->full_name ?></td>
                        </td>
                        <td class="weight"><p>Weight</p><input type= 'text' name= 'weight_<?= $subscriptor->id ?>' value= '<?php if(array_key_exists($subscriptor->id, $list)) echo $list[$subscriptor->id] ?>'></td>
                    </tr></table></li>
                <?php } ?>
            </ul>
            Add a new repartition template
            <table>
                <tr>
                    <td class="check"><input type="checkbox" id="save_template" name="save_template"></td>
                    <td class="template">Save this template</td> 
                    <td>name</td>
                </tr>
            </table>
            <?php if(array_key_exists('whom', $errors)){?>
                <p class="errorMessage"><?php echo $errors['whom'];?></p>
            <?php } ?>
            <input type="submit" value="Save" formaction="operation/edit_operation/<?=$operation->id?>" class="button save">
            <a href="operation/delete_operation/<?= $operation->id ?>" class="button bottom2 delete">Delete this operation</a>
        </form>
    </div>