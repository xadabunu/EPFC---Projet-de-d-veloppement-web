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
    <script>
        let tricount_id, add_btn, subs, table_subs, addables, added, desc_error;
        let title, errTitle, description;

        $(function() {
            add_btn = $("#add_btn");
            table_subs = $("#table_subs");
            subs = <?= $tricount->get_subs_as_json() ?>;
            addables = <?= $tricount->get_addables_as_json() ?>;
            tricount_id = <?= $_GET['param1']?>;
            description = $("#description");
            desc_error = $("#desc_error");
            title = $("#title");
            errTitle = $("#errTitle");

            $("form.nosubmit").submit(function(e) {
                e.preventDefault();
            });
            add_btn.click(addMember);
            description.bind("input", checkDescription);
            title.bind("input", checkTitle);
        })

        function checkTitle() {
            let ok = true;
            title.attr("style", "");
            errTitle.html("");
            if (!(/^.{3,}$/).test(title.val())) {
                errTitle.append("Title lenght must be longer than 3 character");
                ok = false;
                title.attr("style", "border-color: rgb(220, 53, 69)");
            }
            if (ok) {
                checkTitleExists();
            }
            return ok;
        }

        async function checkTitleExists() {
            const data = await $.getJSON("tricount/tricount_exists_service/" + title.val());
            if (data) {
                ok = false;
                errTitle.append("Title already exists");
                title.attr("style", "border-color: rgb(220, 53, 69)");
            }
        }

        function checkDescription() {
            $(description).next(".errorMessage").remove();
            description.attr("style", "");

            if (description.val() !== "" && !(/^.{3,}$/).test(description.val())) {
                description.css("border-color", "rgb(220, 53, 69)");
                description.after("<p class='errorMessage'>Description lenght must be >= 3</p>");
            }
        }

        function checkTitleAndDescription() {
            let ok = checkTitle();
            ok = checkDescription() && ok;
            return ok;
        }

        function my_echo(str, len) {
            let html = "";
            html += str.len > len ? substr(user.name, 0, len - 3) + "..." : str;
            return html;
        }

        async function deleteMember(btn, id) {

            /* -- front -- */
            
            $(btn).closest("tr").hide();
            // doit encore ajouter l'élément supprimé aux addables, à la liste + ordonner
            
            /* -- backend -- */

            try {
                await $.post("tricount/delete_subscriptor_service/" + tricount_id, {"id": id});
            } catch(e) {
                table_subs.html("<tr><td>An error occured</td></tr>");
            }

            return false;
        }

        async function addMember() {

            /* -- front -- */

            //doit encore retirer l'élément des addables et de la liste
            
            added = $("#subscriptor").find(":selected");
            added = addables[added.val()];
            
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
                    current.after(generateHTML(added));
                else
                    current.before(generateHTML(added));
                    
            /* -- back -- */
                await $.post("tricount/add_subscriptor_service/" + tricount_id, {"id": added.id});
        }
            return false;
        }

        function generateHTML(user) {
            let html = "";
            html += "<tr class='pop'>";
            html += "<td>" + my_echo(user.name) + "</td>";
            html += "<td class='link'>"
            html += "<form class='link' onsubmit='deleteMember(this, " + user.id + ");'";
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
            <button form="edittricountform" type="submit" class="button save" id="add">Save</button>
        </header>
        <h3>Settings</h3>
        <form id="edittricountform" action="tricount/edit_tricount/<?= $tricount->id ?>" method="post" class="edit" onsubmit="return checkTitleAndDescription();">
            <label>Title :</label>
            <input id="title" name="title" type="text" value="<?= $tricount->title ?>" <?php if (array_key_exists('required', $errors) || array_key_exists('title_length', $errors) || array_key_exists('unique_title', $errors)) { ?>class="errorInput" <?php } ?>>
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
            <label>Description (optional) :</label>
            <textarea id="description" name="description" rows="3" placeholder="Description" <?php if (array_key_exists('description_length', $errors)) { ?>class="errorInput" <?php } ?>><?= $tricount->description ?></textarea>
            <?php if (array_key_exists('description_length', $errors)) { ?>
                <p id="desc_error" class="errorMessage"><?php echo $errors['description_length']; ?></p>
            <?php } ?>
        </form>
        <h3>Subscriptions</h3>
        <table class="subs" id="table_subs">
            <?php foreach ($tricount->get_subscriptors_with_creator() as $subscriptor) { ?>
                <tr class="pop">
                    <td><?= strlen($subscriptor->full_name) > 30 ? substr($subscriptor->full_name, 0, 30)."..." : $subscriptor->full_name ?> <?= $tricount->creator == $subscriptor ? "<i> (creator)</i>" : "" ?></td>
                    <td class="link">
                        <?php if (in_array($subscriptor, $tricount->get_deletables())) { ?>
                            <form class="link nosubmit" onsubmit="deleteMember(this, <?= $subscriptor-> id ?>);" action='tricount/delete_subscriptor/<?= $tricount->id ?>' method='post'>
                                <input type='text' name='subscriptor_name' value='<?= $subscriptor->id ?>' hidden>
                                <button type="submit" class="pop x"><i class="fa-regular fa-trash-can fa-sm" aria-hidden="true"></i></button>
                            </form>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <form name="subscriptor" method="POST" class="edit nosubmit" onsubmit="addMember();">
            <table>
                <tr>
                    <td class="subscriptor">
                        <select name="subscriptor" id="subscriptor">
                            <option selected disabled>--Add a new subscriber--</option>
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
        <a href="tricount/delete_tricount/<?= $tricount->id ?>" class="button bottom2 delete">Delete this tricount</a>
    </div>
</body>

</html>