<?php 
    require_once "model/RepartitionTemplates.php";
    require_once "model/RepartitionTemplateItems.php"; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= strlen($tricount->title) > 20 ? substr($tricount->title, 0, 20)."..." : $tricount->title ?> &#11208; Edit template</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <script>
        let weights = [];
        const template = {
            title: "<?= $template->title ?>",
            id: "<?= $template->id ?>",
            tricount_id: "<?= $template->tricount->id ?>"
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
            var temp = getWeights();
            return $("#title").val() != template.title || temp.toString() != weights.toString();
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
                        location.replace("templates/manage_templates/" + <?= $_GET['param1'] ?>);
                });
            } else
                location.replace("templates/manage_templates/" + <?= $_GET['param1'] ?>);
        }

        function deleteConfirmed() {
            $.ajax({
				url: "templates/delete_template_service/" + template.id,
				type: "POST",
				dataType: "text",
				cache: false,
				success: Swal.fire({
					title: "Deleted!",
					html: "<p>This template has been deleted</p>",
					icon: "success",
					position: "top",
					confirmButtonColor: "#6f66e2",
					focusConfirm: true
				}).then((result) => {
					location.replace("templates/manage_templates/" + template.tricount_id);
				})
			});
        }

        function confirmDelete() {
            Swal.fire({
                title: "Confirm Template deletion",
                html: `
                    <p>Do you really want to delete template "<b>${template.title}</b>"
                    and all of its dependencies ?</p>
                    <p>This process cannot be undone.</p>
                `,
                icon: 'warning',
                position: 'top',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
        		focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
					deleteConfirmed();
				}
           });
        }


         $(function() {
            let titleAvailable;
            let current_title = <?= json_encode($template->title) ?>;

            const validation = new JustValidate('#edittemplateform', {
                validateBeforeSubmitting : true,
                lockForm : true,
                focusInvalidField : false,
                successLabelCssClass : ['success'],
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

                .onValidate(async function(event) {
                    titleAvailable = await $.post("templates/template_title_available/", {"title" : $("#title").val()}, null, 'json'); 
                    if ($("#title").val() == current_title)
                        titleAvailable = true;
                    if (!titleAvailable)
                        this.showErrors({'#title': 'Title already exists' });
                })

                .onSuccess(function(event) {
                    if(titleAvailable)
                        event.target.submit();
                });

            $("input:text:first").focus();
            $("#back").attr("href", "javascript:confirmBack()");
            $("#delete").attr("href", "javascript:confirmDelete()");
            weights = getWeights(); 

        }); 
        $("#back").attr("href", "javascript:confirmBack()");
        $("#delete").attr("href", "javascript:confirmDelete()");       
    </script>   
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="templates/manage_templates/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= strlen($tricount->title) > 20 ? substr($tricount->title, 0, 20)."..." : $tricount->title ?> &#11208; Edit template</p>
            <button form="edittemplateform" type="submit" class="button save" id="save">Save</button>
        </header>

        <form id="edittemplateform" action="templates/edit_template/<?= $tricount->id ?>/<?= $template->id ?>" method="post" class="edit">
            <label for="title">Title :</label>
            <input id="title" name="title" type="text" size="16" value="<?= $template->title ?>" 
            <?php if (array_key_exists('empty_title', $errors) || array_key_exists('duplicate_title', $errors) || array_key_exists('template_length', $errors)) { ?>class="errorInput" style = "border-color:rgb(220, 53, 69)"<?php } ?>>
            <div id="errorTitle"></div>
            <?php if (array_key_exists('duplicate_title', $errors)) { ?>
                        <p class="errorMessage"><?php echo $errors['duplicate_title']; ?></p>
            <?php } ?>
            <?php if (array_key_exists('empty_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['empty_title']; ?></p>
            <?php }
            if (array_key_exists('template_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['template_length']; ?></p>
            <?php } ?>

                <label>Template items :</label>
                <ul id="whomGroup">
                    <?php foreach ($tricount->get_subscriptors_with_creator() as $subscriptor){
                    if($template->is_participant_template($subscriptor)){
                        $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_and_user($template, $subscriptor);
                    }
                     ?>
                        <li>
                            <table class="whom" <?php  if( (array_key_exists("whom", $errors))  ||  (array_key_exists($subscriptor->id, $list) && !is_numeric($list[$subscriptor->id]) )  ) { ?> style = "border-color:rgb(220, 53, 69)"<?php } ?>>
                                <tr class="edit" id='tr_template_<?= $subscriptor->id ?> weight'>
                                    <td class="check">
                                        <p><input type='checkbox' class="checkbox_template" id='checkbox_<?= $subscriptor->id ?>' <?php echo array_key_exists($subscriptor->id, $list) ? 'checked' : ($template->is_participant_template($subscriptor) ? 'checked' : 'unchecked'); ?> name='<?= $subscriptor->id ?>' value=''></p>
                                    </td>
                                    <td class="user">
                                    <?= strlen($subscriptor->full_name) > 25 ? substr($subscriptor->full_name, 0, 25)."..." : $subscriptor->full_name ?>
                                    </td>
                                    <td class="weight">
                                        <p>Weight</p><input id='weight' class="whom_weight" type='number' name='weight_<?= $subscriptor->id ?>' value='<?php echo array_key_exists($subscriptor->id, $list) ? (is_numeric($list[$subscriptor->id]) ?  $list[$subscriptor->id] : "1")  : ($template->is_participant_template($subscriptor) ? $repartition_template_items->weight : "1"); ?>'>
                                    </td> 
                                </tr>
                            </table>
                        </li>
                    <?php } ?>
                </ul>
            <?php if (array_key_exists('whom', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['whom']; ?></p>
            <?php } ?>
            <?php if (array_key_exists('weight', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['weight']; ?></p>
            <?php } ?>
        </form>
        <a href="templates/delete_template/<?= $template->id ?>/<?= $tricount->id ?>" id="delete" class="button bottom2 delete">Delete this template</a>
    </div>
</body>

</html>