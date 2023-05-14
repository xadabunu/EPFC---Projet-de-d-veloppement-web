<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <title>Add Tricount</title>
    <script>
        let title, errTitle, description, errDescription;


        function checkTitle() {
            let ok = true;
            title.attr("style", "");
            errTitle.html("");
            if (!(/^.{3,}$/).test(title.val())) {
                errTitle.append("Title lenght must be longer than 3 character");
                ok = false;
                title.attr("style", "border-color: rgb(220, 53, 69)");
            }
            return ok;
        }

        async function checkTitleExists() {
            const data = await $.getJSON("Tricount/tricount_exists_service/" + title.val().trim().replaceAll(' ', 'grsgbsigfhfsognlsfaeqe'));
            if (data) {
                errTitle.append("Title already exists");
                title.attr("style", "border-color: rgb(220, 53, 69)");
            }
        }

        function checkDescription() {
            let ok = true;
            errDescription.html("");
            if (description.val() !== "" && !(/^.{3,}$/).test(description.val())) {
                errDescription.append("Description must be empty or longer than 3 character");
                ok = false;
                description.attr("style", "border-color: rgb(220, 53, 69)");
            }
            else {
                description.attr("style", "");
            }
            return ok;
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

        function checkTitleAndDescription() {
            let ok = checkTitle();
            ok = checkDescription() && ok;
            return ok;
        }

        $(function() {
            title = $("#title");
            errTitle = $("#errTitle");
            description = $("#description");
            errDescription = $("#errDescription");

            title.bind("input", checkTitle);
            title.bind("input", checkTitleExists);
            description.bind("input", checkDescription);

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
            <button class="button save" id="add" type="submit" form="add_tricount">Save</button>
        </header>
        <div class="menu">

        </div>
        <form class="edit" id="add_tricount" action="tricount/add_tricount" method="post" onsubmit=" return checkTitleAndDescription();">
            <h3>Titre</h3>
            <input id="title" name="title" type="text" value="<?= $title ?>" placeholder="Title" <?php if (array_key_exists('required', $errors) || array_key_exists('title_length', $errors) || array_key_exists('unique_title', $errors)) { ?>class="errorInput" <?php } ?>>
            <p class = "errorMessage" id="errTitle"></p>
            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('title_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['title_length']; ?></p>
            <?php }
            if (array_key_exists('unique_title', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['unique_title']; ?></p>
            <?php } ?>
            <h3>Description (Optional) :</h3>
            <textarea id="description" name="description" rows="6" placeholder="Description"<?php if (array_key_exists('description_length', $errors)) { ?>class="errorInput" <?php } ?>><?= $description ?></textarea>
            <p class = "errorMessage" id="errDescription"></p>
            <?php if (array_key_exists('description_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['description_length']; ?></p>
            <?php } ?>
        </form>
    </div>
</body>