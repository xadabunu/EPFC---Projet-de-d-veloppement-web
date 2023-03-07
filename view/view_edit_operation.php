<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <title><?= $titleValue ?> &#11208; Edit</title>
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script>
        let op_amount, err_amount, lbl_amount, tr_currency;

        function checkAmount() {
            err_amount.html("");
            tr_currency.attr("style", "");
            if (lbl_amount.val() <= 0) {
                err_amount.append("Amount must be stricly positive");
                tr_currency.attr("style", "border-color: rgb(220, 53, 69)");
            }
        }

        $(function() {
            op_amount = <?= $operation->amount ?>;
            lbl_amount = $("#amount");
            err_amount = $("#errAmount");
            tr_currency = $("#tr_currency");
        })
    </script>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="operation/details/<?= $operation->id ?>" class="button" id="back">Cancel</a>
            <p><?= strlen($titleValue) > 25 ? substr($titleValue, 0, 20)."..." : $titleValue ?> &#11208; Edit</p>
            <button class="button save" id="add" type="submit" form="edit_operation_form">Save</button>
        </header>
        <form id="edit_operation_form" action="operation/edit_operation/<?= $operation->id ?>" method="post" class="edit">
            <input id="title" name="title" type="text" size="16" placeholder="Title" value="<?php if (!is_array($titleValue)) {
                                                                                                echo $titleValue;
                                                                                            } else {
                                                                                                echo $operation->title;
                                                                                            } ?>" <?php if (array_key_exists('empty_title', $errors) || array_key_exists('length', $errors)) { ?>class="errorInput" <?php } ?>>
            <?php if (array_key_exists('empty_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_title']; ?></p>
            <?php }
            if (array_key_exists('length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['length']; ?></p>
            <?php } ?>
            <table class="edit" id="currency">
                <tr class="currency" id="tr_currency">
                    <td><input id="amount" name="amount" type="text" size="16" placeholder="Amount" onchange="checkAmount();" value="<?php if (!is_array($amountValue)) {
                                                                                                                echo $amountValue;
                                                                                                            } else {
                                                                                                                echo $operation->amount;
                                                                                                            } ?>" <?php if (array_key_exists('amount', $errors) || array_key_exists('empty_amount', $errors)) { ?>class="errorInput" <?php } ?>></td>
                    <td class="right">EUR</td>
                </tr>
            </table>
            <p class="errorMessage" id="errAmount">
                <?php if (array_key_exists('amount', $errors)) {
                    echo $errors['amount']; } ?>
            </p>
            <?php if (array_key_exists('empty_amount', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_amount']; ?></p>
            <?php } ?>
            <label for="operation_date">Date</label>
            <input id="operation_date" name="operation_date" type="date" value="<?php if (!is_array($operation_dateValue)) {
                                                                                    echo $operation_dateValue;
                                                                                } else {
                                                                                    echo $operation->operation_date;
                                                                                } ?>" <?php if (array_key_exists('empty_date', $errors)) { ?>class="errorInput" <?php } ?>>
            <?php if (array_key_exists('empty_date', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_date']; ?></p>
            <?php } ?>
            <label for="paid_by">Paid by</label>
            <select name="paid_by" id="paid_by" class="edit edit2">

                <?php if (!is_array($paid_byValue) && !is_string($initiator)) {  ?>    

                    <option value="<?= $paid_byValue->id ?>"><?= strlen($paid_byValue->full_name) > 30 ? substr($paid_byValue->full_name, 0, 30)."..." : $paid_byValue->full_name ?></option>
                <?php } else { ?>
                    <option value="<?= $operation->initiator->id ?>"><?= strlen($operation->initiator->full_name) > 30 ? substr($operation->initiator->full_name, 0, 30)."..." : $operation->initiator->full_name ?></option>
                <?php } ?>

                <?php foreach ($subscriptors as $subscriptor) {
                    if ($subscriptor != $operation->initiator) { ?>
                        <option value="<?= $subscriptor->id ?>"><?= strlen($subscriptor->full_name) > 30 ? substr($subscriptor->full_name, 0, 30)."..." : $subscriptor->full_name ?></option>
                <?php }
                } ?>

            </select>
            <?php if (array_key_exists('empty_initiator', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_initiator']; ?></p>
            <?php } ?>
            <label for="templates">Use repartition template <i>(optional)</i></label>
            <table>
                <tr>
                    <td class="subscriptor">
                        <select name="templates" id="templates" class="edit"> 
                            <?php if (!is_array($templateChoosen) && !is_string($templateChoosen)) {  ?>
                                <option value="<?= $templateChoosen->id ?>" selected><i><?= strlen($templateChoosen->title) > 30 ? substr($templateChoosen->title, 0, 30)."..." : $templateChoosen->title ?></i></option>
                                <option value="No ill use custom repartition" ><i>-- No, i'll use custom repartition --</i></option>
                                <?php foreach ($templates as $template) {
                                    if ($template != $templateChoosen) { ?>
                                        <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30)."..." : $template->title ?></option>
                                <?php }
                                }
                            } else { ?>
                                <option value="No ill use custom repartition" selected>-- No, i'll use custom repartition --</option>
                                <?php foreach ($templates as $template) { ?>
                                    <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30)."..." : $template->title ?></option>
                            <?php }
                            } ?>
                        </select>
                    </td>
                    <td class="subscriptor input"><input type="submit" value="&#8635;" formaction="operation/apply_template_edit_operation/<?= $operation->id ?>"></td>
                </tr>
            </table>
            <label>For whom ? <i>(select at leat one)</i></label>
            <?php if (!is_array($templateChoosen) && !is_string($templateChoosen)) { ?> <!-- Cas ou un template est choisi, on l'affiche donc. -->
                <ul>
                    <?php foreach ($subscriptors as $subscriptor) { ?>
                        <li>
                            <table class="whom">
                                <tr class="edit">
                                    <td class="check">
                                        <p><input type='checkbox' <?php if (array_key_exists($subscriptor->id, $templateUserWeightList)) echo "checked" ?> name='<?= $subscriptor->id ?>' value=''></p>
                                    </td>
                                    <td class="user">
                                    <?= strlen($subscriptor->full_name) > 25 ? substr($subscriptor->full_name, 0, 25)."..." : $subscriptor->full_name ?>
                                    </td>
                                    </td>
                                    <td class="weight">
                                        <p>Weight</p><input type='text' name='weight_<?= $subscriptor->id ?>' value='<?php if (array_key_exists($subscriptor->id, $templateUserWeightList)) {
                                                                                                                            echo $templateUserWeightList[$subscriptor->id];
                                                                                                                        } else{ echo 1;} ?>'>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else if (is_string($templateChoosen)) { if(strcmp($templateChoosen, "No ill use custom repartition") == 0){ ?>    <!-- Cas ou on choisi d'utiliser une répartition custom -->
                <ul>
                    <?php foreach ($subscriptors as $subscriptor) { ?>
                        <li>
                            <table class="whom">
                                <tr class="edit">
                                    <td class="check">
                                        <p><input type='checkbox' checked name='<?= $subscriptor->id ?>' value=''></p>
                                    </td>
                                    <td class="user">
                                    <?= strlen($subscriptor->full_name) > 25 ? substr($subscriptor->full_name, 0, 25)."..." : $subscriptor->full_name ?>
                                    </td>
                                    <td class="weight">
                                        <p>Weight</p><input type='text' name='weight_<?= $subscriptor->id ?>' value='1'>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    <?php } ?>
                </ul> 
            <?php  } } else { ?> <!-- Cas par défaut on affiche les participants du template avec leur poids, sinon non checked avec poids à 1-->
                <ul>
                    <?php foreach ($subscriptors as $subscriptor) { ?>
                        <li>
                            <table class="whom">
                                <tr class="edit">
                                    <td class="check">
                                        <p><input type='checkbox' <?php if (array_key_exists($subscriptor->id, $list)) echo "checked" ?> name='<?= $subscriptor->id ?>' value=''></p>
                                    </td>
                                    <td class="user">
                                    <?= strlen($subscriptor->full_name) > 25 ? substr($subscriptor->full_name, 0, 25)."..." : $subscriptor->full_name ?>
                                    </td>
                                    <td class="weight">
                                        <p>Weight</p><input type='text' name='weight_<?= $subscriptor->id ?>' value='<?php if (array_key_exists($subscriptor->id, $list)) echo $list[$subscriptor->id] ; else{ echo 1;} ?>'>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <?php if (array_key_exists('whom', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['whom']; ?></p>
            <?php } ?>
            <?php if (array_key_exists('weight', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['weight']; ?></p>
            <?php } ?>

            Add a new repartition template
            <table>
                <tr>
                    <td class="check"><input type="checkbox" id="save_template" name="save_template_checkbox"></td>
                    <td class="template">Save this template</td>
                    <td><input id="template_title" name="template_title" type="text" size="16" placeholder="name"></td>

                    <?php if (array_key_exists('empty_template_title', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['empty_template_title']; ?></p>
                    <?php } ?>

                    <?php if (array_key_exists('template_length', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['template_length']; ?></p>
                    <?php } ?>
                </tr>
            </table>
            
            <a href="operation/delete_operation/<?= $operation->id ?>" class="button bottom2 delete delete2">Delete this operation</a>
        </form>
    </div>
</body>

</html>