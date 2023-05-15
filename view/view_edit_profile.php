<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit profile</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script>
        let emailAvailable

         $(function() {
            const validation = new JustValidate('#editprofileform', {
                validateBeforeSubmitting : true,
                lockForm : true,
                focusInvalidField : false,
                successLabelCssClass : ['success'],
                errorLabelCssClass: ['errorMessage'],
                errorFieldCssClass: ['errorInput'],
                successFieldCssClass: ['successField']
            });

            validation
                .addField('#email', [
                    {
                        rule : 'required',
                        errorMessage : 'Email field is required'
                    },
                    {
                        rule : 'maxLength',
                        value : 256,
                        errorMessage : 'Email address cannot be longer than 256 characters'
                    },
                    {
                        rule : 'customRegexp',
                        value : /^[a-zA-Z0-9]{1,20}[@]{1}[a-zA-A0-9]{1,15}[.]{1}[a-z]{1,7}$/,
                        errorMessage : 'Not a valid Email address'
                    },
                ], {errorsContainer : '#errorMail', successMessage : 'Looks good !'})

                .addField('#full_name', [
                    {
                        rule : 'required',
                        errorMessage : 'Full Name field is required'
                    },
                    {
                        rule : 'minLength',
                        value : 3,
                        errorMessage : 'Name length must be between 3 and 256'
                    },
                    {
                        rule : 'maxLength',
                        value : 256,
                        errorMessage : 'Name length must be between 3 and 256'
                    },
                ], {errorsContainer : '#errorName', successMessage : 'Looks good !'})

                .addField('#iban', [
                    {
                        rule : 'customRegexp',
                        value : '/^BE[0-9]{2}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}$/',
                        errorMessage : 'IBAN must have an official Belgian IBAN format'
                    },
                ], {errorsContainer : '#errorIban', successMessage : 'Looks good !'})

                .onValidate((async function(event) {
                    emailAvailable = await $.getJSON("Settings/email_available_service/" + $("#email").val());
                    if (!emailAvailable)
                        this.showErrors({ '#email': 'This email already exists' });
                }))

                .onSuccess(function(event) {
                    if (emailAvailable) {
                        event.target.submit();
                    }
                });

            $("input:text:first").focus();    

        });        
    </script>    
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="settings" class="button" id="back">Back</a>
            <p>Edit profile</p>
            <button form="editprofileform" type="submit" class="button save" id="add">Save</button>
        </header>

        <form id="editprofileform" action="settings/edit_profile" method="post" class="edit">

            <div class="contains_input">
                <span class="icon"><i class="fa-regular fa-at fa-sm" aria-hidden="true"></i></span>
                <input id="email" name="email" type="text" value="<?php if(empty($errors)) {echo $user->email;} else {echo $tmpUser->email;}?>" <?php if (array_key_exists('required', $errors) || array_key_exists('validity', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorMail"></div>

            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('validity', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['validity']; ?></p>
            <?php } ?>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-user fa-sm" aria-hidden="true"></i></span>
                <input id="full_name" name="full_name" type="text" value="<?php if(empty($errors)) {echo $user->full_name;} else {echo $tmpUser->full_name;}?>" <?php if (array_key_exists('length', $errors) || array_key_exists('name_contains', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorName"></div>

            <?php if (array_key_exists('length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['length']; ?></p>
            <?php }
            if (array_key_exists('name_contains', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['name_contains']; ?></p>
            <?php } ?>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-credit-card fa-sm" aria-hidden="true"></i></span>
                <input id="iban" name="iban" type="text" placeholder="IBAN - BE12 3456 7890 1234" value="<?php if(empty($errors)) {echo $user->iban;} else {echo $tmpUser->iban;}?>" <?php if (array_key_exists('iban', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorIban"></div>

            <?php if (array_key_exists('iban', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['iban']; ?></p>
            <?php } ?>
        </form>
    </div>
</body>

</html>