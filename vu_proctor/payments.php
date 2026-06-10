<?php
include 'navbar.php';
// 🗓️ Get today's date and calculate days until month end
$today = date('Y-m-d');
$day = date('j');         // current day (1–31)
$last_day = date('t');    // last day number of this month
$days_left = $last_day - $day;
$month_name = date('F Y');
?>
<div class="container">
    <?php

    // 🧮 Auto-generate on the last day of the month
    if ($day == $last_day) {
        include 'auto_generate_payments.php';
        echo  $msg = "✅ Monthly payment generation completed automatically for $month_name.";
    } else {
        echo $msg = "🕓 $days_left day" . ($days_left != 1 ? "s" : "") . " left until automatic payment generation on " . date('F', strtotime($today)) . " $last_day, " . date('Y') . ".";
    }
    ?>

</div>