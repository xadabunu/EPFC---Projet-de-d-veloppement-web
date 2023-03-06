<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <title>Add Operation</title>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Cancel</a>
            <p>Add Operation</p>
            <button class="button save" id="add" type="submit" form="add_operation_form">Save</button>
        </header>
        <form id="add_operation_form" action="operation/add_operation/<?= $tricount->id ?>" method="post" class="edit">

            <input id="title" name="title" type="text" placeholder="Title" value='<?php if (!is_array($title)) {echo $title;} else {echo '';} ?>' 
                                                                                            <?php if (array_key_exists('empty_title', $errors) || array_key_exists('length', $errors)) { ?>class="errorInput" <?php } ?>>
            <?php if (array_key_exists('empty_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_title']; ?></p>
            <?php }
            if (array_key_exists('length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['length']; ?></p>
            <?php } ?>
            <table class="edit" id="currency">
                <tr class="currency"<?php if (array_key_exists('amount', $errors) || array_key_exists('empty_amount', $errors)) { ?>style = "border-color: rgb(220, 53, 69)" <?php } ?>>
                    <td><input id="Amount" name="amount" type="text" placeholder="Amount" value='<?php if (!is_array($amount)) {
                                                                                                        echo $amount;
                                                                                                    } else {
                                                                                                        echo '';
                                                                                                    } ?>' ></td>
                    <td class="right">EUR</td>
                </tr>
            </table>
            <?php if (array_key_exists("amount", $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['amount']; ?></p>
            <?php }
            if (array_key_exists("empty_amount", $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_amount']; ?></p>
            <?php } ?>
            <label for="operation_date">Date</label>
            <input id="operation_date" name="operation_date" type="date" value='<?php if (!is_array($operation_date)) {
                                                                                    echo $operation_date;
                                                                                } else {
                                                                                    echo '';
                                                                                } ?>' <?php if (array_key_exists('empty_date', $errors)) { ?>class="errorInput" <?php } ?>>
            <?php if (array_key_exists('empty_date', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_date']; ?></p>
            <?php } ?>
            <label for="paid_by">Paid by</label>
            <select name="paid_by" id="paid_by" class="edit edit2" <?php if (array_key_exists('empty_initiator', $errors)) { ?> style = "border-color: rgb(220, 53, 69)" <?php } ?>>
                <?php if (!is_array($initiator) && !is_string($initiator)) { ?>
                    <option value="<?= $initiator->id ?>"><?= strlen($initiator->full_name) > 30 ? substr($initiator->full_name, 0, 30)."..." : $initiator->full_name ?></option>
                <?php } else { ?>
                    <option value=""> -- Who paid for it ? -- </option>
                <?php } ?>
                <?php foreach ($subscriptors as $subscriptor) {
                    if ($subscriptor != $initiator) { ?>
                        <option value="<?= $subscriptor->id ?>"><?= strlen($subscriptor->full_name) > 30 ? substr($subscriptor->full_name, 0, 30)."..." : $subscriptor->full_name ?></option>
                <?php }
                } ?>
            </select>
            <?php if (array_key_exists('empty_initiator', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_initiator']; ?></p>
            <?php } ?>
            <table>
                <tr>
                    <td class="subscriptor">
                        <select name="templates" id="templates" class="edit">
                            <?php if (!is_array($templateChoosen) && !is_string($templateChoosen)) { ?> 
                                <option value="<?= $templateChoosen->id ?>" selected><i><?= strlen($templateChoosen->title) > 30 ? substr($templateChoosen->title, 0, 30)."..." : $templateChoosen->title ?></i></option>
                                <option value="No ill use custom repartition">-- No, i'll use custom repartition --</option>
                                <?php foreach ($templates as $template) {
                                    if ($template != $templateChoosen) { ?>
                                        <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30)."..." : $template->title ?></option>
                                <?php }
                                } ?>
                            <?php } else { ?>
                                <option value="No ill use custom repartition" selected>-- No, i'll use custom repartition --</option>
                                <?php foreach ($templates as $template) { ?>
                                    <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30)."..." : $template->title ?></option>
                            <?php }
                            } ?>
                        </select>
                    </td>
                    <td class="subscriptor input"><input type="submit" value="&#8635;" formaction="operation/apply_template_add_operation/<?= $tricount->id ?>"></td>
                </tr>
            </table>
            <label>For whom ? <i>(select at leat one)</i></label>
                <ul>
                    <?php foreach ($subscriptors as $subscriptor) { ?>
                        <li>
                            <table class="whom" <?php  if( (array_key_exists("whom", $errors)) || (array_key_exists("weight", $errors) && ($subscriptor->id == (int)substr($errors['weight'], 48) ))) { ?> style = "border-color:rgb(220, 53, 69)"<?php } ?>>
                                <tr class="edit">
                                    <td class="check">
                                        <p><input type='checkbox' <?= $userChecked[$subscriptor->id] ?> name='<?= $subscriptor->id ?>' value=''></p>
                                    </td>
                                    <td class="user">
                                    <?= strlen($subscriptor->full_name) > 25 ? substr($subscriptor->full_name, 0, 25)."..." : $subscriptor->full_name ?>
                                    </td>
                                    <td class="weight">
                                        <p>Weight</p><input type='text' name='weight_<?= $subscriptor->id ?>' value='<?= $userWeight[$subscriptor->id] ?>'>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    <?php } ?>
                </ul>
            <?php if (array_key_exists("whom", $errors)) { ?>
                <p class="errorMessage"><?php echo $errors["whom"]; ?></p>
            <?php } ?>
            <?php if (array_key_exists('weight', $errors)) { ?>
                <p class="errorMessage"><?php echo substr($errors['weight'], 0, 48); ?></p>
            <?php } ?>
            Add a new repartition template
            <table <?php if (array_key_exists('empty_template_title', $errors) || array_key_exists('template_length', $errors)) { ?> style = "border-color:rgb(220, 53, 69)"<?php } ?>>
                <tr>
                    <td class="check"><input type="checkbox" id="save_template" name="save_template_checkbox"></td>
                    <td class="template">Save this template</td>
                    <td><input id="template_title" name="template_title" type="text" placeholder="name"></td>
                    <?php if (array_key_exists('empty_template_title', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['empty_template_title']; ?></p>
                    <?php } ?>
                    <?php if (array_key_exists('template_length', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['template_length']; ?></p>
                    <?php } ?>
                    <?php if (array_key_exists('duplicate_title', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['duplicate_title']; ?></p>
                    <?php } ?>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>