<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Change Password</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <script>

        function hasChanges() {
            return $("#password").val() != "" || $("#password_confirm").val() != "" || $("#current_password").val() != "";
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
                        location.replace("settings/my_settings/");
                });
            } else
                location.replace("settings/my_settings/");
        }

         $(function() {
            let passwordIsCorrect;

            const validation = new JustValidate('#changepasswordform', {
                validateBeforeSubmitting : true,
                lockForm : true,
                focusInvalidField : false,
                successLabelCssClass : ['success'],
                errorLabelCssClass: ['errorMessage'],
                errorFieldCssClass: ['errorInput'],
                successFieldCssClass: ['successField']
            });

            validation
            .addField('#current_password', [
                    {
                        rule : 'required',
                        errorMessage : 'Password is required'
                    },
                    {
                        rule: 'minLength',
                        value : 8,
                        errorMessage: 'Minimum 8 characters'
                    },
                    {
                        rule: 'maxLength',
                        value : 16,
                        errorMessage: 'Maximum 16 characters'
                    },
                    {
                        rule: 'customRegexp',
                        value : /[A-Z]/,
                        errorMessage: 'Password must contain an uppercase letter'
                    },
                    {
                        rule: 'customRegexp',
                        value : /\d/,
                        errorMessage: 'Password must contain a digit'
                    },
                    {
                        rule: 'customRegexp',
                        value : /['";:,.\/?\\-]/,
                        errorMessage: 'Password must contain a special character'
                    }
                ], {errorsContainer : '#errorCurrentPassword'})

                .addField("#password", [
                    {
                        rule : 'required',
                        errorMessage : 'Password is required'
                    },
                    {
                        rule: 'minLength',
                        value : 8,
                        errorMessage: 'Minimum 8 characters'
                    },
                    {
                        rule: 'maxLength',
                        value : 16,
                        errorMessage: 'Maximum 16 characters'
                    },
                    {
                        rule: 'customRegexp',
                        value : /[A-Z]/,
                        errorMessage: 'Password must contain an uppercase letter'
                    },
                    {
                        rule: 'customRegexp',
                        value : /\d/,
                        errorMessage: 'Password must contain a digit'
                    },
                    {
                        rule: 'customRegexp',
                        value : /['";:,.\/?\\-]/,
                        errorMessage: 'Password must contain a special character'
                    },
                    {
                        validator : function(value, fields) {
                            if (fields['#current_password'] && fields['#current_password'].elem) {
                                const repeatPasswordValue = fields['#current_password'].elem.value;
                                return value !== repeatPasswordValue;
                            }
                            return true;
                        },
                        errorMessage : 'You have to enter a password different from the current '
                    }
                ], {errorsContainer : '#errorPassword'})

                .addField('#password_confirm', [
                    {
                        rule: 'required',
                        errorMessage: 'Field is required'
                    },
                    {
                        rule: 'minLength',
                        value : 8,
                        errorMessage: 'Minimum 8 characters'
                    },
                    {
                        rule: 'maxLength',
                        value : 16,
                        errorMessage: 'Maximum 16 characters'
                    },
                    {
                        rule: 'customRegexp',
                        value : /[A-Z]/,
                        errorMessage: 'Password must contain an uppercase letter'
                    },
                    {
                        rule: 'customRegexp',
                        value : /\d/,
                        errorMessage: 'Password must contain a digit'
                    },
                    {
                        rule: 'customRegexp',
                        value : /['";:,.\/?\\-]/,
                        errorMessage: 'Password must contain a special character'
                    },
                    {
                        validator : function(value, fields) {
                            if (fields['#password'] && fields['#password'].elem) {
                                const repeatPasswordValue = fields['#password'].elem.value;
                                return value === repeatPasswordValue;
                            }
                            return true;
                        },
                        errorMessage : 'You have to enter twice the same password '
                    }
                ], {errorsContainer : '#errorPasswordconfirm'})

                .onValidate(async function(event) {
                    passwordIsCorrect = await $.post("Settings/current_password_is_correct/" , {"password" : $("#current_password").val()}, null, 'json'); //+ $("#current_password").val());
                    if (!passwordIsCorrect)
                        this.showErrors({'#current_password': 'Wrong current password' });
                })

                .onSuccess(function(event) {
                    if(passwordIsCorrect)
                        event.target.submit();
                });

            $("input:text:first").focus();
            $("#back").attr("href", "javascript:confirmBack()")    

        });        
    </script>    
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="settings" class="button" id="back">Back</a>
            <p>Change Password</p>
            <button form="changepasswordform" type="submit" class="button save" id="add">Save</button>
        </header>

        <form id="changepasswordform" action="settings/change_password" method="post" class="edit">

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm" aria-hidden="true"></i></span>
                <input id="current_password" name="current_password" type="password" placeholder="Current password" value="" <?php if (array_key_exists('current_wrong_password', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorCurrentPassword"></div>

            <?php if (array_key_exists('current_wrong_password', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['current_wrong_password']; ?></p>
            <?php } ?>

            <br><br>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm" aria-hidden="true"></i></span>
                <input id="password" name="password" type="password" placeholder="New password" value="<?= $password ?>" <?php if (array_key_exists('password_length', $errors) || array_key_exists('password_validity', $errors) || array_key_exists('password_confirm', $errors) || array_key_exists('password_format', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorPassword"></div>

            <?php if (array_key_exists('password_length', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_length']; ?></p>
            <?php }
            if (array_key_exists('password_format', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_format']; ?></p>
            <?php } ?>

            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm" aria-hidden="true"></i></span>
                <input id="password_confirm" name="password_confirm" type="password" placeholder="Confirm your new password" value="<?= $password_confirm ?>" <?php if (array_key_exists('password_length', $errors) || array_key_exists('password_validity', $errors) || array_key_exists('password_confirm', $errors) || array_key_exists('password_format', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div id="errorPasswordconfirm"></div>

            <?php if (array_key_exists('password_confirm', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_confirm']; ?></p>
            <?php } ?>

            <?php if (array_key_exists('password_validity', $errors)) { ?>
                <p class="errorMessage"><?php echo $errors['password_validity']; ?></p>
            <?php } ?>
            
        </form>

    </div>
</body>

</html>