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
        let op_amount, err_amount, lbl_amount, tr_currency, for_whom_table, err_whom;

        function checkAmount() {
            err_amount.html("");
            tr_currency.attr("style", "");
            if (lbl_amount.val() <= 0) {
                err_amount.append("Amount must be stricly positive");
                tr_currency.css("border-color", "rgb(220, 53, 69)");
            }
            else
                updateAmounts();
        }

        function updateAmounts() {
            let amount = lbl_amount.val() > 0 ? lbl_amount.val() : op_amount;
            let sum_weight = 0;

            $(".whom tr").each(function() {
                if ($(this).find("input:checkbox").is(":checked"))
                    sum_weight += parseInt($(this).find(".whom_weight").val());
            });

            $(".whom tr").each(function() {
                if ($(this).find("input:checkbox").is(":checked")) {
                    let w = $(this).find(".whom_weight").val();
                    $(this).find(".user_amount").html(Math.round(100 * w * amount / sum_weight)/100 + " €");
                }
                else
                    $(this).find(".user_amount").html("0 €");
            })
        }

        function checkWeight(e) {
            err_whom.html("");
            $(e).parent().parent().attr("style", "");            

            var x =  $(e).find(".whom_weight");
            var g = $(e).find("input:checkbox");

            if ($(e).find("input:checkbox").is(":checked")) {

                if (x.val() < 0) {
                    g.prop("checked", false);
                    $(e).parent().parent().css("border-color", "rgb(220, 53, 69)");
                    err_whom.html("weights can not be negative");
                }
                else {    
                    g.prop("checked", x.val() != 0);
                }

            }
            updateAmounts();
            updateTemplate();
        }

        function updateTemplate() {
            choosing_template.prop("value", "No ill use custom repartition");
        }

        async function checkTemplate() {
            let elem_val = choosing_template.val();
            if (jQuery.isNumeric(elem_val)) {
                let obj = await $.getJSON("Operation/get_repartition_template_by_id_as_json/" + elem_val);
                applyTemplate(obj.id);
            }
        }

        async function applyTemplate(template_id) {
            let json = await $.getJSON("Operation/get_repartition_template_items_by_repartition_template_id_as_json/" + template_id);

            $(".checkbox_template").each(function() {
                $(this).prop("checked", false);
            })
            $(".whom_weight").each(function() {
                $(this).prop("value", "1");
            })

            for(let item of json) {
                if($("input[name='checkbox_" + item.user + "']").length > 0) {
                    $("input[name='checkbox_" + item.user + "']").prop("checked", true);
                    $("input[name='weight_" + item.user + "']").val(item.weight);
                } 
            }
        }

        $(function() {
            op_amount = <?= $operation->amount ?>;
            lbl_amount = $("#amount");
            err_amount = $("#errAmount");
            tr_currency = $("#tr_currency");
            for_whom_table = $("#for_whom");
            err_whom = $("#errWhom");
            template_dom = $("#template");
            choosing_template = $("#templates");
            $("#button_apply_template").hide();
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
            <input id="title" name="title" type="text" size="16" placeholder="Title" value="<?php if (!empty($titleValue)) {
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
                    <td><input id="amount" name="amount" type="text" size="16" placeholder="Amount" onchange="checkAmount();" value="<?php if (!empty($amountValue)) {
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
            <input id="operation_date" name="operation_date" type="date" value="<?php if (!empty($operation_dateValue)) {
                                                                                    echo $operation_dateValue;
                                                                                } else {
                                                                                    echo $operation->operation_date;
                                                                                } ?>" <?php if (array_key_exists('empty_date', $errors)) { ?>class="errorInput" <?php } ?>>
            <?php if (array_key_exists('empty_date', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_date']; ?></p>
            <?php } ?>
            <label for="paid_by">Paid by</label>
            <select name="paid_by" id="paid_by" class="edit edit2">

                <?php if (!empty($paid_byValue)) {  ?>    

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
                <tr onchange="checkTemplate();">
                    <td class="subscriptor">
                        <select name="templates" id="templates" class="edit"> 
                            <?php if (!empty($templateChoosen)) {  ?>
                                <option value="<?= $templateChoosen->id ?>" selected><i><?= strlen($templateChoosen->title) > 30 ? substr($templateChoosen->title, 0, 30)."..." : $templateChoosen->title ?></i></option>
                                <option value="No ill use custom repartition" ><i>-- No, i'll use custom repartition --</i></option>
                                <?php foreach (RepartitionTemplates::get_all_repartition_templates_by_tricount_id($operation->tricount->id) as $template) {
                                    if ($template != $templateChoosen) { ?>
                                        <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30)."..." : $template->title ?></option>
                                <?php }
                                }
                            } else { ?>
                                <option value="No ill use custom repartition" selected>-- No, i'll use custom repartition --</option>
                                <?php foreach (RepartitionTemplates::get_all_repartition_templates_by_tricount_id($operation->tricount->id) as $template) { ?>
                                    <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30)."..." : $template->title ?></option>
                            <?php }
                            } ?>
                        </select>
                    </td>
                    <td class="subscriptor input" id="button_apply_template"><input type="submit"  value="&#8635;" formaction="operation/apply_template_edit_operation/<?= $operation->id ?>"></td>
                </tr>
            </table>
            <label>For whom ? <i>(select at leat one)</i></label>
                <ul>
                    <?php foreach ($operation->tricount->get_subscriptors_with_creator() as $subscriptor){ 
                        if (!empty($templateChoosen) && $templateChoosen->is_participant_template($subscriptor)) {
                            $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_and_user($templateChoosen, $subscriptor);}
                        else {
                            $repartition_template_items = '';
                        }
                        if ($operation->is_participant_operation($subscriptor)) {
                            $repartition = Repartitions::get_repartition_by_operation_and_user_id($operation->id, $subscriptor);
                        }
                        else {
                            $repartition = '';
                        }
                     ?>
                        <li>
                            <table class="whom" <?php  if((array_key_exists("whom", $errors)) || (array_key_exists($subscriptor->id, $list) && !is_numeric($list[$subscriptor->id]))) { ?> style = "border-color:rgb(220, 53, 69)"<?php } ?>>
                                <tr class="edit" id="template" onchange="checkWeight(this);">
                                    <td class="check">
                                        <p><input class="checkbox_template" type='checkbox' name='checkbox_<?= $subscriptor->id ?>' <?php echo empty($errors) ? (empty($templateChoosen) ? ($operation->is_participant_operation($subscriptor) ? 'checked' : 'unchecked') : (empty($repartition_template_items) ? 'unchecked' :  'checked' )) : (array_key_exists($subscriptor->id, $list) ? 'checked' : 'unchecked');?> name='<?= $subscriptor->id ?>' value=''></p>
                                    </td>
                                    <td class="user">
                                    <?= strlen($subscriptor->full_name) > 25 ? substr($subscriptor->full_name, 0, 25)."..." : $subscriptor->full_name ?>
                                    </td>
                                    <td class="weight" id="td_amount">
                                        <p>Amount</p>
                                        <div class="user_amount"><?= round($operation->get_user_amount($subscriptor->id), 2) ?> €</div>
                                    </td>
                                    <td class="weight">
                                        <p>Weight</p>
                                        <input type='text' class="whom_weight" name='weight_<?= $subscriptor->id ?>' value='<?php echo empty($errors) ? (empty($templateChoosen) ? ($operation->is_participant_operation($subscriptor) ? $repartition->weight : '1') : (empty($repartition_template_items) ? '1' : $repartition_template_items->weight)) : (array_key_exists($subscriptor->id, $list) ? (is_numeric($list[$subscriptor->id]) ? $list[$subscriptor->id] : "1") : ('1')); ?>'>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    <?php } ?>
                </ul>
                <p class="errorMessage" id="errWhom">
                    <?php if (array_key_exists('whom', $errors)) { echo $errors['whom']; } ?>
                </p>
            <?php if (array_key_exists('weight', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['weight']; ?></p>
            <?php } ?>

            Add a new repartition template
            <table <?php if (array_key_exists('empty_template_title', $errors) || array_key_exists('template_length', $errors)) { ?> style = "border-color:rgb(220, 53, 69)"<?php } ?>>
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
                    <?php if (array_key_exists('duplicate_title', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['duplicate_title']; ?></p>
                    <?php } ?>
                </tr>
            </table>
            
            <a href="operation/delete_operation/<?= $operation->id ?>" class="button bottom2 delete delete2">Delete this operation</a>
        </form>
    </div>
</body>

</html>