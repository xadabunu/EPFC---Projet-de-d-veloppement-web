<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <title>Add Tricount</title>
    <script>
        let title, errTitle, description, errDescription, titleExists;

        <?php if (!Configuration::get("JustValidate")) { ?>

            async function checkTitle() {
                let ok = true;
                title.attr("style", "");
                errTitle.html("");
                if (title.val() === "") {
                    errTitle.append("Title is required");
                    ok = false;
                    title.attr("style", "border-color: rgb(220, 53, 69)");
                    $("#add").attr("type", "button");
                }
                else if (!(/^.{3,}$/).test(title.val())) {
                    errTitle.append("Title length must be between 3 and 256");
                    ok = false;
                    title.attr("style", "border-color: rgb(220, 53, 69)");
                    $("#add").attr("type", "button");
                } else
                $("#add").attr("type", "submit");
                if (ok) {
                    const data = await $.post("Tricount/tricount_exists_service/", {"title" : title.val()}, null, 'json');
                if (data) {
                    errTitle.append("You already named a tricount with this title");
                    title.attr("style", "border-color: rgb(220, 53, 69)");
                    $("#add").attr("type", "button");
                } else
                    $("#add").attr("type", "submit");
                }
                return ok;
            }

            function checkDescription() {
                let ok = true;
                errDescription.html("");
                if (description.val() !== "" && !(/^.{3,1024}$/).test(description.val())) {
                    errDescription.append("If not empty, description lenght must be between 3 and 1024");
                    ok = false;
                    $("#add").attr("type", "button");
                    description.attr("style", "border-color: rgb(220, 53, 69)");
                }
                else {
                    $("#add").attr("type", "submit");
                    description.attr("style", "");
                }
                return ok;
            }

            function checkTitleAndDescription() {
                let ok = checkTitle();
                ok = checkDescription() && ok;
                return ok;
            }
        <?php } ?>


        function debounce (fn, time) {
            var timer;
            return function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fn.apply(this, arguments);
                }, time);
            }
        }


        function confirmBack() {
            if (description.val() != "" || title.val() != "") {
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
                        location.replace("user/my_tricounts/");
                    });
                } else
                    location.replace("user/my_tricounts/");
        }            

        $(function() { 
            title = $("#title");
            errTitle = $("#errorTitle");
            description = $("#description");
            errDescription = $("#errorDescription");
            
            <?php if (Configuration::get("JustValidate")) { ?>
                
                errTitle.attr("class", ""); 
                errDescription.attr("class", "");

                const validation = new JustValidate('#add_tricount_form', {
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
                            rule : 'required',
                            errorMessage : 'Title is required'
                        },
                        {
                            rule : 'minLength',
                            value : 3,
                            errorMessage : 'Title length must be between 3 and 256',
                            
                        },
                        {
                            rule : 'maxLength',
                            value : 256,
                            errorMessage : 'Title length must be between 3 and 256'
                        },
                    ], {errorsContainer : "#errorTitle"})

                    .addField('#description', [
                        {
                            validator : function(value) {
                                if (value !== "") {
                                    $("#description").addClass("errorInput");
                                    if(value.length > 2 && value.length < 1024){
                                        $("#description").removeClass("errorInput");
                                    }
                                    return value.length > 2 && value.length < 1024 ;
                                }
                                $("#description").removeClass("errorInput");
                                return true;
                            },
                            errorMessage : 'If not empty, description lenght must be between 3 and 1024'
                        }
                    ], {errorsContainer : "#errorDescription"})

                    .onValidate(debounce(async function(event) {
                        titleExists = await $.post("Tricount/tricount_exists_service/", {"title" : title.val()}, null, 'json');
                        if (titleExists){
                            this.showErrors({ '#title': 'You already named a tricount with this title' });
                        } 
                    }, 300))

                    .onSuccess(function(event) {
                        if (!titleExists)
                            event.target.submit();
                    });

            <?php } 

            else { ?>
                title.bind("input", checkTitle);
                description.bind("input", checkDescription);

            <?php } ?>
            
            $("#back").attr("href", "javascript:confirmBack()");
            $("input:text:first").focus();
        });


    </script>    
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="main/index" class="button" id="back">Cancel</a>
            <p>Add Tricount</p>
            <button class="button save" id="add" type="submit" form="add_tricount_form">Save</button>
        </header>
        <div class="menu"></div>
        <form class="edit" id="add_tricount_form" action="tricount/add_tricount" method="post" <?php if (!Configuration::get("JustValidate")) {?> onsubmit=" return checkTitleAndDescription();"<?php }?>>
            <h3>Titre</h3>
            <div class="contains_input">
                <input id="title" name="title" type="text" value="<?= $title ?>" placeholder="Title" <?php if (array_key_exists('required', $errors) || array_key_exists('title_length', $errors) || array_key_exists('unique_title', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorTitle" class="errorMessage">
            <?php if (array_key_exists('required', $errors)) {
                echo $errors['required'];
                echo "<br><br>";
                echo $errors['title_length'];
            }
            else if (array_key_exists('title_length', $errors)) {
                echo $errors['title_length'];
            }
            else if (array_key_exists('unique_title', $errors)) {
                echo $errors['unique_title'];
            } ?>
            </div>
            <h3>Description (Optional) :</h3>
            <div id="contains_input">
                <textarea id="description" name="description" rows="6" placeholder="Description"<?php if (array_key_exists('description_length', $errors)) { ?>class="errorInput" <?php } ?>><?= $description ?></textarea>
            </div>
            <div id="errorDescription" class="errorMessage">
                <?php if (array_key_exists('description_length', $errors)) {
                echo $errors['description_length'];
            } ?>
            </div>
        </form>
    </div>
</body>