<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset="utf-8">
    <title>Sign up</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script>
        let emailAvailable;

        function debounce (fn, time) {
            var timer;

            return function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fn.apply(this, arguments);
                }, time);
            }
        }

        $(function() {
            const validation = new JustValidate('#signupform', {
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
                ], {errorsContainer : '#errorMail'})

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
                ], {errorsContainer : '#errorName'})

                .addField('#password', [
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
                    },
                ], {errorsContainer : '#errorPassword_confirm'})

                .addField('#iban', [
                    {
                        rule : 'customRegexp',
                        value : /^BE[0-9]{2}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}$/,
                        errorMessage : 'IBAN must have an official Belgian IBAN format'
                    },
                ], {errorsContainer : '#errorIban'})

                .onValidate((async function(event) {
                    emailAvailable = await $.post("Main/email_available_service/", {"email" : $("#email").val()}, null, 'json');
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
        <header class="t1"><span class="icon"><i class="fa-solid fa-dragon fa-xl" aria-hidden="true"></i></span>Sign Up</header>
        <form id="signupform" action="main/signup" method="post" class="connect2">
            <div class="formtitle">Sign Up</div>
            <div class="contains_input">
                <span class="icon"><i class="fa-regular fa-at fa-sm icon_position" aria-hidden="true"></i></span>
                <input class="input_with_icon" autocomplete="off" id="email" name="email" type="text" placeholder="Email" value="<?= $email ?>" <?php if (array_key_exists('required', $errors) || array_key_exists('validity', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div class="error_with_icon" id="errorMail"></div>
            <?php if (array_key_exists('required', $errors)) { ?>
                <p class="errorMessage error_with_icon"><?php echo $errors['required']; ?></p>
            <?php }
            if (array_key_exists('validity', $errors)) { ?>
                <p class="errorMessage error_with_icon "><?php echo $errors['validity']; ?></p>
            <?php } ?>
            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-user fa-sm icon_position" aria-hidden="true"></i></span>
                <input class="input_with_icon" id="full_name" name="full_name" type="text" placeholder="Full Name" value="<?= $full_name ?>" <?php if (array_key_exists('length', $errors) || array_key_exists('name_contains', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div class="error_with_icon" id="errorName"></div>
            <?php if (array_key_exists('length', $errors)) { ?>
                <p class="errorMessage error_with_icon"><?php echo $errors['length']; ?></p>
            <?php }
            if (array_key_exists('name_contains', $errors)) { ?>
                <p class="errorMessage error_with_icon"><?php echo $errors['name_contains']; ?></p>
            <?php } ?>
            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-credit-card fa-sm icon_position" aria-hidden="true"></i></span>
                <input class="input_with_icon" id="iban" name="iban" type="text" placeholder="IBAN - BE12 3456 7890 1234" value="<?= $iban ?>" <?php if (array_key_exists('iban', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div class="error_with_icon" id="errorIban"></div>
            <?php if (array_key_exists('iban', $errors)) { ?>
                <p class="errorMessage error_with_icon"><?php echo $errors['iban']; ?></p>
            <?php } ?>
            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm  icon_position" aria-hidden="true"></i></span>
                <input class="input_with_icon" id="password" name="password" type="password" placeholder="Password" value="<?= $password ?>" <?php if (array_key_exists('password_length', $errors) || array_key_exists('password_format', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div class="error_with_icon" id="errorPassword"></div>
            <?php if (array_key_exists('password_length', $errors)) { ?>
                <p class="errorMessage error_with_icon"><?php echo $errors['password_length']; ?></p>
            <?php }
            if (array_key_exists('password_format', $errors)) { ?>
                <p class="errorMessage error_with_icon"><?php echo $errors['password_format']; ?></p>
            <?php } ?>
            <div class="contains_input">
                <span class="icon"><i class="fa-solid fa-lock fa-sm  icon_position" aria-hidden="true"></i></span>
                <input class="input_with_icon" id="password_confirm" name="password_confirm" type="password" placeholder="Confirm your password" value="<?= $password_confirm ?>" <?php if (array_key_exists('password_confirm', $errors)) { ?>class="errorInput" <?php } ?>>
            </div>
            <div class="error_with_icon" id="errorPassword_confirm"></div>
            <?php if (array_key_exists('password_confirm', $errors)) { ?>
                <p class="errorMessage error_with_icon"><?php echo $errors['password_confirm']; ?></p>
            <?php } ?>
            <input class='login' type="submit" value="Sign Up">
            <a href="index.php" class="button bottom" id='back'>Cancel</a>
        </form>
    </div>
</body>

</html>