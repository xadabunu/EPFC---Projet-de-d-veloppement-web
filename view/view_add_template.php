<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8">
    <title><?= $tricount->title ?> &#11208; New template</title>
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="main">
    <header class="t2">
            <a href="templates/manage_templates/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= $tricount->title ?> &#11208; New template</p>
            <button form= "addtemplateform" type="submit" class ="button save" id="save">Save</button> 
    </header>

    <form id="addtemplateform" action="templates/add_template/<?= $tricount->id ?>" method="post" class="edit">
    <label for="title">Title :</label>
    <input id="title" name="title" type="text" size="16" <?php if(array_key_exists('empty_title', $errors) || array_key_exists('lenght', $errors)) {?>class = "errorInput"<?php } ?>>

    <?php if (array_key_exists('empty_title', $errors)){ ?>
            <p class="errorMessage"><?php echo $errors['empty_title'];?></p>
        <?php } 
            if(array_key_exists('template_lenght', $errors)){?>
            <p class="errorMessage"><?php echo $errors['lenght'];?></p>
        <?php } ?>

    <label for="whom">Template items :</label>
        <ul>
            <?php foreach($subscriptors as $subscriptor){?>
                 <li>
                    <table class="whom">
                        <tr class="edit">
                         <td class="check">
                            <p><input type='checkbox' name='<?= $subscriptor->id ?>' value='' checked></p>
                         </td>
                         <td class="user">
                            <?= $subscriptor->full_name ?></td>
                         </td>
                         <td class="weight"><p>Weight</p><input type= 'text' name= 'weight_<?= $subscriptor->id ?>' value= '1'></td>
                        </tr>
                    </table>
                 </li>
            <?php } ?>
        </ul>

        <?php if(array_key_exists('whom', $errors)){?>
                <p class="errorMessage"><?php echo $errors['whom'];?></p>
        <?php } ?>

    </form>

    </div>
</body>
</html>