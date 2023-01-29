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
            <button class = "button save" id="add" type="submit" form="edit_operation_form">Save</button>
        </header>
        <form id="edit_operation_form" action="operation/edit_operation/<?=$operation->id?>" method="post" class="edit">
            <input id="title" name="title" type="text" size="16" placeholder="Title" value= "<?php if(!is_array($titleValue)){echo $titleValue;} else{ echo $operation->title;} ?>" <?php if(array_key_exists('empty_title', $errors) || array_key_exists('lenght', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if (array_key_exists('empty_title', $errors)){ ?>
                <p class="errorMessage"><?php echo $errors['empty_title'];?></p>
            <?php } 
            if (array_key_exists('lenght', $errors)) {?>
                <p class="errorMessage"><?php echo $errors['lenght'];?></p>
            <?php } ?>
            <table class="edit" id="currency">
                <tr class="currency">
                    <td><input id="amount" name="amount" type="text" size="16" placeholder="Amount" value= "<?php if(!is_array($amountValue)){echo $amountValue;} else{ echo $operation->amount;} ?>" <?php if(array_key_exists('amount', $errors) || array_key_exists('empty_amount', $errors)) {?>class = "errorInput"<?php } ?>></td>
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
            <input id="operation_date" name="operation_date" type="date" value = "<?php if(!is_array($operation_dateValue)){echo $operation_dateValue;} else{ echo $operation->operation_date;} ?>" <?php if(array_key_exists('empty_date', $errors)) {?>class = "errorInput"<?php } ?>>
            <?php if(array_key_exists('empty_date', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_date'];?></p>
            <?php } ?>
            <label for="paid_by">Paid by</label>
            <select name="paid_by" id="paid_by" class="edit edit2">

            <?php if(!is_array($paid_byValue)){  ?>

                <option value="<?= $paid_byValue->id ?>" ><?= $paid_byValue->full_name ?></option>
            <?php } else{ ?>
                <option value="<?= $operation->initiator->id ?>" ><?= $operation->initiator->full_name ?></option>
            <?php } ?>

                <?php foreach($subscriptors as $subscriptor) { 
                    if($subscriptor != $operation->initiator){ ?>
                        <option value="<?= $subscriptor->id ?>"><?= $subscriptor->full_name ?></option>
                    <?php } } ?>


            </select>
            <?php if(array_key_exists('empty_initiator', $errors)){?>
                <p class="errorMessage"><?php echo $errors['empty_initiator'];?></p>
            <?php } ?>



            <label for="templates">Use repartition template <i>(optional)</i></label>


            <table>
                <td class="subscriptor">
                 <select name="templates" id="templates" class=edit> <!-- class css probablement à modifier -->
                    <?php if(!is_array($templateChoosen)){  ?>
                        <option  value ="<?= $templateChoosen->id ?>" selected><i><?= $templateChoosen->title ?></i></option>
                        <option ><i>-- No, i'll use custom repartition --</i></option>
                        <?php foreach($templates as $template) { ?>
                          <option value="<?= $template->id ?>"><?= $template->title ?></option>
                        <?php } ?>


                    <?php } else{ ?>
                        <option selected><i>-- No, i'll use custom repartition --</i></option>
                        <?php foreach($templates as $template) { ?>
                          <option value="<?= $template->id ?>"><?= $template->title ?></option>
                        <?php } }?>
                 </select>
                </td>

                <td class="subscriptor input"><input type="submit" value="Add" formaction="operation/apply_template_edit_operation/<?= $operation->id ?>"></td>
            </table>





            <?php if(!is_array($templateChoosen)) { ?>

                <label for="whom">For whom ? <i>(select at leat one)</i></label>
            <ul>
                <?php foreach($subscriptors as $subscriptor){?>
                    <li>
                    <table class="whom">
                    <tr class="edit">
                        <td class="check">
                            <p><input type='checkbox' <?php if (array_key_exists($subscriptor->id, $templateUserWeightList)) echo "checked"?> name='<?= $subscriptor->id ?>' value=''></p>
                        </td>
                        <td class="user">
                            <?= $subscriptor->full_name ?></td>
                        </td>
                        <td class="weight"><p>Weight</p><input type= 'text' name= 'weight_<?= $subscriptor->id ?>' value= '<?php if(array_key_exists($subscriptor->id, $templateUserWeightList)){ echo $templateUserWeightList[$subscriptor->id]; } else { echo 1;}?>'></td>
                    </tr></table></li>
                <?php } ?>
            </ul>


            <?php } else { ?>

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
                            <?= $subscriptor->full_name ?>
                        </td>
                        <td class="weight"><p>Weight</p><input type= 'text' name= 'weight_<?= $subscriptor->id ?>' value= '<?php if(array_key_exists($subscriptor->id, $list)) echo $list[$subscriptor->id] ?>'></td>
                    </tr></table></li>
                <?php } ?>
            </ul>

            <?php } ?>




            Add a new repartition template
            <table>
                <tr>
                    <td class="check"><input type="checkbox" id="save_template" name="save_template_checkbox"></td>
                    <td class="template">Save this template</td> 
                    <td><input id="template_title" name="template_title" type="text" size="16" placeholder="name"></td>

                    <?php if(array_key_exists('empty_template_title', $errors)){?>
                        <p class="errorMessage"><?php echo $errors['empty_template_title'];?></p>
                    <?php } ?>

                    <?php if(array_key_exists('template_lenght', $errors)){?>
                        <p class="errorMessage"><?php echo $errors['template_lenght'];?></p>
                    <?php } ?>


                </tr>
            </table>


            
            
            <?php if(array_key_exists('whom', $errors)){?>
                <p class="errorMessage"><?php echo $errors['whom'];?></p>
            <?php } ?>
            <a href="operation/delete_operation/<?= $operation->id ?>" class="button bottom2 delete delete2">Delete this operation</a>
        </form>
    </div>