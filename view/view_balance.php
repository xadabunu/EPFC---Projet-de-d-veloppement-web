<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= $web_root ?>">
    <link href="css/styles.css" rel="stylesheet" type="text/css">
    <title><?= $tricount->title ?> &#11208; balance</title>
    <script src="lib/jquery-3.6.3.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"
            integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let balance_table, canvas;
        
        $(function() {
            balance_table = $("#balance_table");
            
            balance_table.display = "none";
            balance_table.before("<canvas id='myChart'></canvas>");
            balance_table.remove();
            generateCanvas();
        })

        function generateCanvas() {
            canvas = $("#myChart");
            const ctx = canvas[0].getContext("2d");
            const subs = <?= $tricount->get_subs_as_json() ?>
            
            let amounts = [];
            let background_colors= [];
            let max = 0;
            const session = <?= json_encode($_SESSION) ?>;

            <?php foreach ($amounts as $amount) { ?>
                a = <?= $amount ?>;
                amounts.push(a);
                if (Math.abs(a) > max)
                    max = Math.abs(a);
                background_colors.push(a > 0 ? "rgb(60, 179, 113)" : "rgb(255, 99, 71)");
            <?php } ?>

            let labels = [];
            for (let sub of subs) {
                if (session.user.id == sub.id)
                    sub.name += " (me)";
                labels.push(sub.name);
            }

            const chart = new Chart(ctx, {
                type: "bar",

                data: {
                    labels: labels,
                    datasets: [{
                        color: "rgb(0, 0, 0)",
                        backgroundColor: background_colors,
                        borderRadius: Number.MAX_VALUE,
                        data: amounts
                    }]
                },
                plugins: [ChartDataLabels],
                options: {
                    indexAxis: "y",
                    scales: {
                        x: {
                            display: false,
                            suggestedMax: max,
                            suggestedMin: -max
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 18,
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            color: "rgb(0, 0, 0)",
                            formatter: function (value) {
                                return value + " €";
                            },
                            font: {
                                weight: "bold",
                                size: 16
                            }
                        }
                    }
                }
            });
        }

    </script>
</head>

<body>
    <div class="main">
        <header class="t2">
            <a href="tricount/operations/<?= $tricount->id ?>" class="button" id="back">Back</a>
            <p><?= strlen($tricount->title) > 25 ? substr($tricount->title, 0, 22)."..." : $tricount->title ?> &#11208; balance</p>
            <p></p>
        </header>
        <?php if (count($tricount->get_subscriptors_with_creator()) == 1) { ?>
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
            <?php } else {?>
        <table class="balance" id="balance_table">
            <?php foreach ($tricount->get_subscriptors_with_creator() as $sub) { ?>
                <tr class="balance">
                    <?php if ($amounts[$sub->id] >= 0) { ?>
                        <td class="balance">
                            <p class="left<?php if ($sub->id === $user->id) {
                                                echo " bold";
                                            } ?>"><?= strlen($sub->full_name) > 10 ? substr($sub->full_name, 0, 10)."..." : $sub->full_name ?><?php if ($sub->id === $user->id) {
                                                                                                                            echo " (me)";
                                                                                                                        } ?></p>
                        </td>
                        <td class="positive balance">
                            <p class="<?php if ($amounts[$sub->id] != 0) {echo "positive";} ?> right<?php if ($sub->id === $user->id) {echo " bold";} ?>" style="width: <?= $max == 0 ? 0 : abs($amounts[$sub->id] / $max * 100) ?>%;">
                            <p class="inner"><?= round($amounts[$sub->id], 2) ?> €</p>
                        </td>
                    <?php } else { ?>
                        <td class="negative balance">
                            <p class="negative left" style="width: <?= abs($amounts[$sub->id] / $max * 100) ?>%;">
                            <p class="inner left<?php if ($sub->id === $user->id) {echo " bold";} ?>"><?= round($amounts[$sub->id], 2) ?> €</p>
                        </td>
                        <td class="balance">
                            <p class="right<?php if ($sub->id === $user->id) {echo " bold";} ?>"><?= strlen($sub->full_name) > 25 ? substr($sub->full_name, 0, 22)."..." : $sub->full_name ?><?php if ($sub->id === $user->id) {echo " (me)";} ?></p>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
        <?php } ?>
    </div>

</body>

</html>