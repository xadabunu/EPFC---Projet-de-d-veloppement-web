<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?= $tricount->title ?> &#11208; Edit</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="lib/sweetalert2@11.js"></script>
    <script src="lib/just-validate-4.2.0.production.min.js" type="text/javascript"></script>
    <script src="lib/just-validate-plugin-date-1.2.0.production.min.js" type="text/javascript"></script>
    <script>
        let titleAvailable;
        let table_subs, added, desc_error, title, errTitle, description;
        const user_id = "<?= $user->id ?>";
        const db_title = "<?= $tricount->title ?>";
        const db_description = "<?= $tricount->description ?>";
		const tricount = {
                id: <?= $tricount->id ?>,
                title: "<?= $tricount->title ?>",
            };
        let addables = <?= $tricount->get_addables_as_json() ?>;
        const tricount_id = <?= $_GET['param1']?>;
        let subs = <?= $tricount->get_subs_as_json() ?>;

        $(function() {

            description = $("#description");
            table_subs = $("#table_subs");
            description = $("#description");
            desc_error = $("#errorDescription");
            title = $("#title");
            errTitle = $("#errorTitle");

            $("form.nosubmit").submit(function(e) {
                e.preventDefault();
            });
            $("#delete").attr("href", "javascript:confirmDelete()");
            $("#back").attr("href", "javascript:confirmBack()");
            
            $("input:text:first").focus();

            <?php if (Configuration::get("JustValidate")) { ?>

                errTitle.attr("class", ""); 
                desc_error.attr("class", "");

            const validation = new JustValidate('#edit_tricount_form', {
                validateBeforeSubmitting : true,
                lockForm : true,
                focusInvalidField : false,
                successLabelCssClass : ['success'],
                errorFieldCssClass: ['errorInput'],
                errorLabelCssClass: ['errorMessage'],
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
                        errorMessage : 'Title length must be between 3 and 256'
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
                    titleAvailable = await $.post("Tricount/tricount_exists_service/",
                                                                    {"title" : $("#title").val(),
                                                                    "tricount_id" : tricount_id }, null, 'json');
                    if (titleAvailable)
                        this.showErrors({ '#title': 'This title already exists' });
                }, 300))

                .onSuccess(function(event) {
                    if (!titleAvailable)
                        event.target.submit();
                })

                <?php } else { ?>

                    description.bind("input", checkDescription);
                    title.bind("input", checkTitle);
                <?php } ?>
        })

        <?php if (Configuration::get("JustValidate")) { ?>

        function debounce (fn, time) {
            var timer;

            return function() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fn.apply(this, arguments);
                }, time);
            }
        }

        <?php } else { ?>

        function checkTitle() {
            let ok = true;
            title.attr("style", "");
            errTitle.html("");
            if (!(/^.{3,}$/).test(title.val())) {
                errTitle.append("Title length must be between 3 and 256");
                ok = false;
                title.attr("style", "border-color: rgb(220, 53, 69)");
                $("#add").attr("type", "button");
            }
            if (ok)
                ok = checkTitleExists();
            return ok;
        }

        async function checkTitleExists() {
            const data = await $.post("tricount/tricount_exists_service", { title: title.val(), tricount_id: tricount_id });
            if (data != "false") {
                $("#add").attr("type", "button");
                errTitle.append("Title already exists");
                title.attr("style", "border-color: rgb(220, 53, 69)");
                return false;
            }
            $("#add").attr("type", "submit");
            return true;
        }

        function checkDescription() {
            description.attr("style", "");
            desc_error.html("");

            if (description.val() !== "" && !(/^.{3,1024}$/).test(description.val())) {
                description.css("border-color", "rgb(220, 53, 69)");
                desc_error.html("If not empty, description lenght must be between 3 and 1024");
                $("#add").attr("type", "button");
            }
            else
                $("#add").attr("type", "submit");
        }

        async function checkTitleAndDescription() {
            let ok = await checkTitle();
            ok = checkDescription() && ok;
            return ok;
        }
        <?php } ?>

		async function deleteConfirmed() {
			$.ajax({
				url: "tricount/delete_tricount_service/" + tricount_id,
				type: "POST",
				dataType: "text",
				cache: false,
				success: Swal.fire({
					title: "Deleted!",
					html: "<p>This tricount has been deleted</p>",
					icon: "success",
					position: "top",
					confirmButtonColor: "#6f66e2",
					focusConfirm: true
				}).then((result) => {
					location.replace("user/my_tricounts");
				})
			});
		}

        function confirmDelete() {
            Swal.fire({
                title: "Confirm Tricount deletion",
                html: `
                    <p>Do you really want to delete tricount "<b>${tricount.title}</b>"
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

        function confirmBack() {
            if (db_title.trim() != title.val().trim() || db_description.trim() != description.val().trim()) {
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
                        location.replace("tricount/operations/" + tricount_id);
                    });
                } else
                    location.replace("tricount/operations/" + tricount_id);
        }

        function my_echo(str, len) {
            let html = "";
            html += str.len > len ? substr(str, 0, len - 3) + "..." : str;
            return html;
        }

        async function deleteSubscriptor(btn, id) {

            /* -- front -- */
            
            $(btn).closest("tr").hide();
            addables[id] = {
                "name" : $(btn).parents().parents().children("td:first").html(),
                "id" : id
            }

             let current = $("form[name='subscriptor']").find("option:first");
             let next = $(current).next();

            while (addables[id].name > current.html() && $(next).html()) {
                current = next;
                next = $(current).next();
            }

            if (addables[id].name > current.html())
                current.after(generateSelectHTML(addables[id]));
            else
                current.before(generateSelectHTML(addables[id]));
                        
            /* -- backend -- */

            try {
                await $.post("tricount/delete_subscriptor_service/" + tricount_id, {"id": id});
            } catch(e) {
                table_subs.html("<tr><td>An error occured</td></tr>");
            }

            if (id == user_id)
                location.reload();

            return false;
        }

        async function addSubscriptor() {

            /* -- front -- */
            
            added = $("#subscriptor").find(":selected");
            if (added.val() != -1) {
            const tmp = added.val();
                $("#subscriptor").val("-1").change();
                added.remove();
                added = addables[tmp];
                
                if (added) {
                    let current = table_subs.find("tr:first");
                    let td_name = $(current).find("td:first");
                    let next = $(current).next("tr");

                    while (added.name > td_name.html() && $(next).find("td:first").html()) {
                        current = next;
                        next = $(next).next("tr");
                        td_name = $(current).find("td:first");
                    }

                    if (added.name > td_name.html())
                        current.after(generateTableHTML(added));
                    else
                        current.before(generateTableHTML(added));
                    $(addables).splice(added.id);
                        
                /* -- back -- */

                    await $.post("tricount/add_subscriptor_service/" + tricount_id, {"id": added.id});
                }
            }
            return false;
        }

        function generateSelectHTML(deleted) {
            let html = "<option value='" + deleted.id + "'>";
            html += my_echo(deleted.name, 20);
            html += "</option>";

            return html;
        }

        function generateTableHTML(user) {
            let html = "";
            html += "<tr class='pop'>";
            html += "<td>" + my_echo(user.name) + "</td>";
            html += "<td class='link'>"
            html += "<form class='link' onsubmit='deleteSubscriptor(this, " + user.id + ");'";
            html += "action='javascript:void(0);'" + "method='post'>";
            html += "<input type='text' name='subscriptor_name' value='" + user.id + "' hidden>";
            html += "<button type='submit' class='pop x'><i class='fa-regular fa-trash-can fa-sm' aria-hidden='true'></i></button>";
            html += "</form>";
            html += "</td>"
            html += "</tr>";
            return html;
        }
    </script>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= strlen($tricount->title) > 20 ? substr($tricount->title, 0, 20)."..." : $tricount->title ?> &#11208; Edit</p>
            <button form="edit_tricount_form" type="submit" class="button save" id="add">Save</button>
        </header>
        <h3>Settings</h3>
        <form id="edit_tricount_form" action="tricount/edit_tricount/<?= $tricount->id ?>" method="post" class="edit" <?php if (!Configuration::get("JustValidate")) {?> onsubmit=" return checkTitleAndDescription();"<?php }?> >
            <label>Title :</label>
            <input id="title" name="title" type="text" value="<?= $tricount->title ?>" <?php if (array_key_exists('required', $errors) || array_key_exists('title_length', $errors) || array_key_exists('unique_title', $errors)) { ?>class="errorInput" <?php } ?>>
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
                <textarea id="description" name="description" rows="6" placeholder="Description"<?php if (array_key_exists('description_length', $errors)) { ?>class="errorInput" <?php } ?>><?= $tricount->description ?></textarea>
            </div>
            <div id="errorDescription" class="errorMessage">
                <?php if (array_key_exists('description_length', $errors)) {
                echo $errors['description_length'];
            } ?>
            </div>
        </form>
        <h3>Subscriptions</h3>
        <table class="subs" id="table_subs">
            <?php foreach ($tricount->get_subscriptors_with_creator() as $subscriptor) { ?>
                <tr class="pop">
                    <td><?= strlen($subscriptor->full_name) > 30 ? substr($subscriptor->full_name, 0, 30)."..." : $subscriptor->full_name ?> <?= $tricount->creator == $subscriptor ? "<i> (creator)</i>" : "" ?></td>
                    <td class="link">
                        <?php if (in_array($subscriptor, $tricount->get_deletables())) { ?>
                            <form class="link nosubmit" onsubmit="deleteSubscriptor(this, <?= $subscriptor-> id ?>);" action='tricount/delete_subscriptor/<?= $tricount->id ?>' method='post'>
                                <input type='text' name='subscriptor_name' value='<?= $subscriptor->id ?>' hidden>
                                <button type="submit" class="pop x"><i class="fa-regular fa-trash-can fa-sm" aria-hidden="true"></i></button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <form name="subscriptor" method="POST" class="edit nosubmit" onsubmit="addSubscriptor();">
            <table>
                <tr>
                    <td class="subscriptor">
                        <select name="subscriptor" id="subscriptor">
                            <option id="cbo_box" selected value="-1">--Add a new subscriber--</option>
                            <?php foreach ($tricount->get_cbo_users() as $cbo_user) { ?>
                                <option value="<?= $cbo_user->id ?>"><?= strlen($cbo_user->full_name) > 20 ? substr($cbo_user->full_name, 0, 20)."..." : $cbo_user->full_name ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="subscriptor input">
                        <input id="add_btn" type="submit" value="Add" formaction="tricount/add_subscriptors/<?= $tricount->id ?>">
                    </td>
                </tr>
            </table>
        </form>
        <a href="templates/manage_templates/<?= $tricount->id ?>" class="button bottom2 manage">Manage repartition template</a>
        <a href="tricount/delete_tricount/<?= $tricount->id ?>" id="delete" class="button bottom2 delete">Delete this tricount</a>
    </div>
</body>

</html>