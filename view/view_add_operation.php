<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <title>Add Operation</title>
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <script>
        let op_amount, err_amount, lbl_amount, tr_currency, for_whom_table, err_whom;
        let date = new Date().toISOString().substring(0, 10);
        let operation = {
            weights: [],
            paid_by: <?= $operation->initiator->id ?>
        };
        let titleAvailable;

        function checkAmount() {
            err_amount.html("");
            tr_currency.attr("style", "");
            if (lbl_amount.val() <= 0) {
                err_amount.append("Amount must be stricly positive");
                tr_currency.attr("style", "border-color: rgb(220, 53, 69)");
            } else
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
                    $(this).find(".user_amount").html(Math.round(100 * w * amount / sum_weight) / 100 + " €");
                } else
                    $(this).find(".user_amount").html("0 €");
            })
        }

        function checkWeight(e) {
            err_whom.html("");
            $(e).parent().parent().attr("style", "");

            var x = $(e).find(".whom_weight");
            var g = $(e).find("input:checkbox");

            if ($(e).find("input:checkbox").is(":checked")) {

                if (x.val() < 0) {
                    g.prop("checked", false);
                    $(e).parent().parent().css("border-color", "rgb(220, 53, 69)");
                    err_whom.html("weights can not be negative");
                } else {
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

            for (let item of json) {
                if ($("#checkbox_" + item.user).length > 0) {
                    $("#checkbox_" + item.user).prop("checked", true);
                    $("input[name='weight_" + item.user + "']").val(item.weight);
                }
            }
            updateAmounts();
        }

        function saveTemplateCheckbox() {
            if ($("#save_template").is(":checked")) {
                $("#template_title").prop("disabled", false);
                $("#td_template_title").css("background-color", "white");
            }
            else {
                $("#template_title").prop("disabled", true);
                $("#td_template_title").css("background-color", "rgb(233, 236, 239)");
            }
        }

        function getWeights() {
            var table = [];
            $("table.whom tr").each((i, elem) => {
                var check = $(elem).find(".checkbox_template");
                if ($(check).prop("checked")) {
                    table[$(check).attr("id").substring(9)] = $(elem).find(".whom_weight").val();
                }
            });
            return table;
        }

        function hasChanges() {
            return $("#title").val() != "" ||
            $("#amount").val() != "" ||
            $("#operation_date").val() != date ||
            operation.paid_by != $("#paid_by").val() ||
            operation.weights.toString() != getWeights().toString();
        }

        function confirmBack() {
            if (hasChanges()) {
                Swal.fire({
                    title: "Unsaved changes !",
                    html: `
                        <p>Are you sure you want to leave this form ?
                        Changes you made will not be saved.</p>
                    `,
                    icon: 'warning',
                    position: 'top',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c747c',
                    confirmButtonText: 'Leave Page',
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed)
                        location.replace("tricount/operations/" + <?= $_GET['param1'] ?>);
                });
            } else
                location.replace("tricount/operations/" + <?= $_GET['param1'] ?>);
        }

        function debounce(fn, time) {
            var timer;

            return function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fn.apply(this, arguments);
                }, time);
            }
        }

        $(function() {

            <?php if (Configuration::get("JustValidate")) { ?>

                const validation = new JustValidate('#add_operation_form', {
                    validateBeforeSubmitting: true,
                    lockForm: true,
                    focusInvalidField: false,
                    successLabelCssClass: ['success'],
                    errorLabelCssClass: ['errorMessage'],
                    errorFieldCssClass: ['errorInput'],
                    successFieldCssClass: ['successField']
                    
                });

                validation
                    .addField('#title', [
                        {
                            rule: 'required',
                            errorMessage: 'Title is required'
                        },
                        {
                            rule: 'minLength',
                            value: 3,
                            errorMessage: 'Title length must be between 3 and 256',

                        },
                        {
                            rule: 'maxLength',
                            value: 256,
                            errorMessage: 'Title length must be between 3 and 256'
                        },
                    ], {errorsContainer: "#errorTitle"})

                    .addField('#amount', [
                        {
                            rule : 'required',
                            errorMessage : 'Amount field cannot be empty'
                        },
                        {
                            rule : 'number',
                            errorMessage : 'Amount  must be a number'
                        },
                        {
                            rule : 'minNumber',
                            value : 0.01,
                            errorMessage : 'Amount must be superior than 0,01'
                        }

                    ], {errorsContainer: "#errorAmount"})

                    .addField('#operation_date', [
                        {
                            rule : 'required',
                            errorMessage : 'Operation date is required'
                        },
                        {
                        plugin : JustValidatePluginDate(() => {
                            return {
                                format : 'yyyy-mm-dd',
                                isBeforeOrEqual : date
                            };
                        }),
                                errorMessage: 'Date should be before the date of the day'
                        }
                    ], {errorsContainer : '#errorOperation_date'})

                    .addField('#paid_by', [
                        {
                            rule : 'required',
                            errorMessage : 'You have to select an initiator'
                        }
                    ], {errorsContainer: "#errorPaidBy"})

                    .addRequiredGroup(
                        '#whomGroup',
                        'You should select at least one participant'
                    )

                    .addField("#weight", [
                        {
                            rule : 'integer',
                            errorMessage : 'Weight must be an integer'
                        },
                        {
                            rule : 'minNumber',
                            value : 0,
                            errorMessage : 'Weight must be positive'
                        }
                    ], {errorsContainer : "#errorWeight"})

                    .addField("#template_title", [
                        {
                            validator: (value) => {
                                return !$("#save_template").is(":checked") || $("#template_title").val();
                            },
                            errorMessage : 'You have to name your template',  
                        },
                        {
                            validator: (value) => {
                                return !$("#save_template").is(":checked") || $("#template_title").val().length >= 3;
                            },
                            errorMessage : 'Title length must be between 3 and 256',  
                        },
                        {
                            validator: (value) => {
                                return !$("#save_template").is(":checked") || $("#template_title").val().length <= 256;
                            },
                            errorMessage : 'Title length must be between 3 and 256', 

                        }
                    ], {errorsContainer : '#save_template_error'})

                    .onValidate(async function(event) {
                        titleAvailable = await $.post("operation/template_title_available/" , {"title" : $("#template_title").val()}, null, 'json');
                        if (!titleAvailable)
                            this.showErrors({ '#template_title': 'Name already exists' });
                    })

                    .onSuccess(function(event) {
                        if(titleAvailable)
                            event.target.submit();
                    });

            <?php } ?>    

            op_amount = <?= empty($operation->amount) ? 0 : $operation->amount ?>;
            lbl_amount = $("#amount");
            err_amount = $("#errAmount");
            tr_currency = $("#tr_currency");
            err_whom = $("#errWhom");
            for_whom_table = $("#for_whom");
            choosing_template = $("#templates");
            $("#button_apply_template").hide();
            updateAmounts();
            $("input:text:first").focus();
            $("#template_title").prop("disabled", true);
            $("#td_template_title").css("background-color", "rgb(233, 236, 239)");
            $("#back").attr("href", "javascript:confirmBack()")
            operation.weights = getWeights();
        });
    </script>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $_GET['param1'] ?>" class="button" id="back">Cancel</a>
            <p>Add Operation</p>
            <button class="button save" id="add" type="submit" form="add_operation_form">Save</button>
        </header>
        <form id="add_operation_form" action="operation/add_operation/<?= $_GET['param1'] ?>" method="post" class="edit">
            <div id="contains_input">
                <input id="title" name="title" type="text" placeholder="Title" value='<?php if (!empty($operation->title)) {
                                                                                        echo $operation->title;
                                                                                    } else {
                                                                                        echo '';
                                                                                    } ?>' <?php if (array_key_exists('empty_title', $errors) || array_key_exists('length', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorTitle"></div>
            <?php if (array_key_exists('empty_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_title']; ?></p>
            <?php }
            if (array_key_exists('length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['length']; ?></p>
            <?php } ?>
            <table class="edit" id="currency">
                <tr class="currency" id="tr_currency" <?php if (array_key_exists('amount', $errors) || array_key_exists('empty_amount', $errors)) { ?>style="border-color: rgb(220, 53, 69)" <?php } ?>>
                    <td>
                        <input id="amount" name="amount" type="text" placeholder="Amount" onchange="checkAmount();" value='<?php if (!empty($operation->amount)) {
                                                                                                                                echo $operation->amount;
                                                                                                                            } else {
                                                                                                                                echo '';
                                                                                                                            } ?>'<?php if (array_key_exists("amount", $errors) || array_key_exists("empty_amount", $errors)) { ?>class="errorInput" <?php } ?>>
                    </td>
                    <td class="right">EUR</td>
                </tr>
            </table>
            <div id="errorAmount"></div>
            <p class="errorMessage" id="errAmount">
                <?php if (array_key_exists("amount", $errors)) {
                    echo $errors['amount'];
                } ?>
            </p>
            <?php
            if (array_key_exists("empty_amount", $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_amount']; ?></p>
            <?php } ?>
            <label for="operation_date">Date</label>
            <input id="operation_date" name="operation_date" type="date" value='<?php if (!empty($operation->operation_date)) {
                                                                                    echo $operation->operation_date;
                                                                                } else {
                                                                                    echo '';
                                                                                } ?>' <?php if (array_key_exists('empty_date', $errors)) { ?>class="errorInput" <?php } ?>>
            
            <div id="errorOperation_date"></div>
            <?php if (array_key_exists('empty_date', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_date']; ?></p>
            <?php } ?>
            <label for="paid_by">Paid by</label>
            <select name="paid_by" id="paid_by" class="edit edit2" <?php if (array_key_exists('empty_initiator', $errors)) { ?> style="border-color: rgb(220, 53, 69)" <?php } ?>>
                <?php if (!is_null($operation->initiator)) { ?>
                    <option value="<?= $operation->initiator->id ?>"><?= strlen($operation->initiator->full_name) > 30 ? substr($operation->initiator->full_name, 0, 30) . "..." : $operation->initiator->full_name ?></option>
                <?php } else { ?>
                    <option value=""> -- Who paid for it ? -- </option>
                <?php } ?>
                <?php foreach (Tricount::get_tricount_by_id($_GET['param1'])->get_subscriptors_with_creator() as $subscriptor) {
                    if ($subscriptor != $operation->initiator) { ?>

                        <option value="<?= $subscriptor->id ?>"><?= strlen($subscriptor->full_name) > 30 ? substr($subscriptor->full_name, 0, 30) . "..." : $subscriptor->full_name ?></option>
                <?php }
                } ?>
            </select>
            <div id="errorPaidBy"></div><div class="success"></div>
            <?php if (array_key_exists('empty_initiator', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_initiator']; ?></p>
            <?php } ?>
            <label for="templates">Use repartition template <i>(optional)</i></label>
            <table>
                <tr onchange="checkTemplate();">
                    <td class="subscriptor">
                        <select name="templates" id="templates" class="edit">
                            <?php if (!empty($templateChoosen)) { ?>
                                <option value="<?= $templateChoosen->id ?>" selected><i><?= strlen($templateChoosen->title) > 30 ? substr($templateChoosen->title, 0, 30) . "..." : $templateChoosen->title ?></i></option>
                                <option value="No ill use custom repartition">-- No, i'll use custom repartition --</option>
                                <?php foreach (RepartitionTemplates::get_all_repartition_templates_by_tricount_id($_GET['param1']) as $template) {
                                    if ($template != $templateChoosen) { ?>
                                        <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30) . "..." : $template->title ?></option>
                                <?php }
                                } ?>
                            <?php } else { ?>
                                <option value="No ill use custom repartition" selected>-- No, i'll use custom repartition --</option>
                                <?php foreach (RepartitionTemplates::get_all_repartition_templates_by_tricount_id($_GET['param1']) as $template) { ?>
                                    <option value="<?= $template->id ?>"><?= strlen($template->title) > 30 ? substr($template->title, 0, 30) . "..." : $template->title ?></option>
                            <?php }
                            } ?>
                        </select>
                    </td>
                    <td class="subscriptor input" id="button_apply_template"><input type="submit" value="&#8635;" formaction="operation/apply_template_add_operation/<?= $_GET['param1'] ?>"></td>

                </tr>
            </table>
            <label>For whom ? <i>(select at leat one)</i></label>
            <ul id="whomGroup">
                <?php foreach (Tricount::get_tricount_by_id($_GET['param1'])->get_subscriptors_with_creator() as $subscriptor) {
                    if (!empty($templateChoosen) && $templateChoosen->is_participant_template($subscriptor)) {
                        $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_and_user($templateChoosen, $subscriptor);
                    } else {
                        $repartition_template_items = '';
                    }
                ?>
                    <li>
                        <table class="whom" <?php if ((array_key_exists("whom", $errors)) || (array_key_exists($subscriptor->id, $list) && !is_numeric($list[$subscriptor->id]))) { ?> style="border-color:rgb(220, 53, 69)" <?php } ?>>
                            <tr class="edit" id='tr_template_<?= $subscriptor->id ?>' onchange="checkWeight(this);">
                                <td class="check">
                                    <p><input type='checkbox' class="checkbox_template" id='checkbox_<?= $subscriptor->id ?>' <?php echo empty($list) ? (empty($templateChoosen) ? 'checked' : (empty($repartition_template_items) ? 'unchecked' : 'checked')) : (array_key_exists($subscriptor->id, $list) ? 'checked' : 'unchecked'); ?> name='<?= $subscriptor->id ?>' value=''></p>
                                </td>
                                <td class="user">
                                    <?= strlen($subscriptor->full_name) > 25 ? substr($subscriptor->full_name, 0, 25) . "..." : $subscriptor->full_name ?>
                                </td>
                                <td class="weight td_amount" id="td_amount_<?= $subscriptor->id ?>">
                                    <p>Amount</p>
                                    <div class="user_amount">0 €</div>
                                </td>
                                <td class="weight">
                                    <p>Weight</p>
                                    <input id='weight_<?= $subscriptor->id ?>' type='number' class="whom_weight" name='weight_<?= $subscriptor->id ?>' value='<?php echo empty($list) ? (empty($templateChoosen) ? '1' : (empty($repartition_template_items) ? '1' : $repartition_template_items->weight)) : (array_key_exists($subscriptor->id, $list) ? (is_numeric($list[$subscriptor->id]) ? $list[$subscriptor->id] : "1") : ('1')); ?>'>
                                </td>
                            </tr>
                        </table>
                    </li>
                <?php } ?>
            </ul>
            <div id ="errorWeight"></div>
            <?php if (array_key_exists("whom", $errors)) { ?>
                <p class="errorMessage"><?php echo $errors["whom"]; ?></p>
            <?php } ?>
            <?php if (array_key_exists('weight', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['weight']; ?></p>
            <?php } ?>
            Add a new repartition template
            <table class="table_with_no_margin" <?php if (array_key_exists('empty_template_title', $errors) || array_key_exists('template_length', $errors) || array_key_exists('duplicate_title', $errors)) { ?> style = "border-color:rgb(220, 53, 69)"<?php } ?>>
                <tr>
                    <td class="check" oninput="saveTemplateCheckbox();"><input type="checkbox" id="save_template" name="save_template_checkbox"></td>
                    <td class="template">Save this template</td>
                    <td id="td_template_title" ><div style="color:silver">Name</div><input id="template_title" name="template_title" type="text" size="16" value='<?php if (!empty($repartition_template)) {echo $repartition_template->title;} else {echo '';} ?>'></td>
                </tr>
            </table>
            <div id="save_template_error"></div>
                    <?php if (array_key_exists('empty_template_title', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['empty_template_title']; ?></p>
                    <?php } ?>
                    <?php if (array_key_exists('template_length', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['template_length']; ?></p>
                    <?php } ?>
                    <?php if (array_key_exists('duplicate_title', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['duplicate_title']; ?></p>
                    <?php } ?>
        </form>
    </div>
</body>

</html>