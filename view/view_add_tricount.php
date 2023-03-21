<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <title>Add Tricount</title>
    <script>
        let title, errTitle, description, errDescription;

        document.onreadystatechange = function() {
            if(document.readyState === 'complete') {
                title = document.getElementById("title");
                errTitle = document.getElementById("errTitle");
                description = document.getElementById("description");
                errDescription = document.getElementById("errDescription");
            }
        };

        function checkTitle() {
            let ok = true;
            title.setAttribute("style", "");
            errTitle.innerHTML = "";
            if(!(/^.{3,}$/).test(title.value)) {
                errTitle.innerHTML += "Title lenght must be longer than 3 character";
                ok = false;
                title.setAttribute("style", "border-color: rgb(220, 53, 69)");
            }
            return ok;
        }

        function checkDescription() {
            let ok = true;
            errDescription.innerHTML = "";
            if(description !== "" && !(/^.{3,}$/).test(description.value)) {
                errDescription.innerHTML += "Description must be empty or longer than 3 character";
                ok = false;
                description.setAttribute("style", "border-color: rgb(220, 53, 69)");
            }
            else {
                description.setAttribute("style", "");
            }
            return ok
        }

        function checkTitleAndDescription() {
            let ok = checkTitle();
            ok= checkDescription() && ok;
            return ok;
        }


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
        <form class="edit" id="add_tricount" action="tricount/add_tricount" method="post" onsubmit=" return checkTitleAndDescription()">
            <h3>Titre</h3>
            <input id="title" name="title" type="text" value="<?= $title ?>" placeholder="Title" oninput="checkTitle();"<?php if (array_key_exists('required', $errors) || array_key_exists('title_length', $errors) || array_key_exists('unique_title', $errors)) { ?>class="errorInput" <?php } ?>>
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
            <textarea id="description" name="description" rows="6" placeholder="Description" oninput="checkDescription();" <?php if (array_key_exists('description_length', $errors)) { ?>class="errorInput" <?php } ?>><?= $description ?></textarea>
            <p class = "errorMessage" id="errDescription"></p>
            <?php if (array_key_exists('description_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['description_length']; ?></p>
            <?php } ?>
        </form>
    </div>
</body>