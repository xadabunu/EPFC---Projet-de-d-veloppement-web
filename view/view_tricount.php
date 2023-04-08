<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <script src="lib/jquery-3.6.3.js" type="text/javascript"></script>
    <title><?= $tricount->title ?> &#11208; Expenses</title>
    <script>
        const operations = <?= $operations_json ?>;
        let tblOperations;
        let sortChoice = "operation_date";
        let sortAscending = false;

        document.onreadystatechange = function() {
            if (document.readyState === 'complete') {
                tblOperations = document.getElementById('operation_list');
                if (tblOperations) {   
                    document.getElementById('sort').onchange = function() {
                        sort(this.value);
                    }
                }    
            }
        };

        function displayTable() {
            let html = "";
            for (let op of operations) {
                html += "<tr>";
                html += "<td><p><b><a href = 'operation/details/"+ op.id + "'> " + op.title + "</b></p>" + "<p>" + "Paid by " + op.initiator + "</p>" + "</td>";
                html += "<td class = right>" + "<p><b>" + op.amount + "€" + "</b></p>" + "<p>" + op.operation_date + "</td>";
                html += "</tr>"
            }
            tblOperations.innerHTML = html;
        }

        function sortOperation() {
            operations.sort(function(a, b) {
                if (a[sortChoice] < b[sortChoice]) {
                    return sortAscending ? -1 : 1;
                }
                if (a[sortChoice] > b[sortChoice]) {
                    return sortAscending ? 1 : -1;
                }
                return 0;
            })
        }

        function sort(field) {
            if (field === sortChoice)
                sortAscending = !sortAscending;
            else {
                sortChoice = field;
                sortAscending = true;
            }
            sortOperation();
            displayTable();
        }
    </script>

</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="main/index/<?= $tricount->id ?>" class="button" id="back">Home</a>
            <p><?= strlen($tricount->title) <= 20 ? $tricount->title : substr($tricount->title, 0, 18) . "..." ?> &#11208; Expenses</p>
            <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button" id="add">Edit</a>
        </header>
        <?php if (empty($list)) { ?>
            <?php if ($alone) { ?>
                <table>
                    <tr>
                        <th class="empty">You are alone!</th>
                    </tr>
                    <tr>
                        <td class="empty">
                            <p>Click below to add your friends!</p>
                            <a href="tricount/edit_tricount/<?= $tricount->id ?>" class="button">Add Friends</a>
                        </td>
                    </tr>
                </table>
            <?php } else { ?>
                <table>
                    <tr>
                        <th class="empty">Your tricount is empty!</th>
                    </tr>
                    <tr>
                        <td class="empty">
                            <p>Click below to add your first expense!</p>
                            <a href="operation/add_operation/<?= $tricount->id ?>" class="button">Add an expense</a>
                        </td>
                    </tr>
                </table>
            <?php }
        } else { ?>
            <p class="balance"><a href="tricount/balance/<?= $tricount->id ?>" class="button" id="balance"><b>&#8644;</b> View balance</a></p>
            <p style="font-size : 80%">Order expenses by:
                <select id="sort" class="selectCSS">
                    <option value="operation_date">Date  &emsp; &#9650;</option>
                    <option value="operation_date" selected>Date &emsp; &#9660;</option>
                    <option value="amount">Amount &emsp; &#9650;</option>
                    <option value="amount">Amount &emsp; &#9660;</option>
                    <option value="initiator">Paid by &emsp; &#9650;</option>
                    <option value="initiator">Paid by &emsp; &#9660;</option>
                    <option value="title">Title &emsp; &#9650;</option>
                    <option value="title">Title &emsp; &#9660;</option>
                </select>
            </p>
            <table id="operation_list">
                <?php foreach ($list as $operation) { ?>
                    <tr>
                        <td>
                            <p><b><a href="operation/details/<?= $operation->id ?>"><?= strlen($operation->title) > 30 ? substr($operation->title, 0, 25) . "..." : $operation->title ?></a></b></p>
                            <p>Paid by <?= strlen($operation->initiator->full_name) > 25 ? substr($operation->initiator->full_name, 0, 23) : $operation->initiator->full_name ?></p>
                        </td>
                        <td class="right">
                            <p><b><?= number_format($operation->amount, 2) ?> €</b></p>
                            <p><?= date("d/m/Y", strtotime($operation->operation_date)) ?></p>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
        <footer>
            <div>
                <p>MY TOTAL</p>
                <p><b><?= number_format($user_total, 2) ?> €</b></p>
            </div>
            <a href="operation/add_operation/<?= $tricount->id ?>" class="circle">+</a>
            <div>
                <p>TOTAL EXPENSES</p>
                <p><b><?= number_format($total, 2) ?> €</b></p>
            </div>
        </footer>
    </div>
</body>

</html>