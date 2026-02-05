<?php
session_start();
include "../config/db.php";


/* GET TOTALS */
$income = $conn->query("
SELECT SUM(amount) total FROM income
WHERE status='verified'
")->fetch_assoc()['total'] ?? 0;

$expense = $conn->query("
SELECT SUM(amount) total FROM expenses
")->fetch_assoc()['total'] ?? 0;

$balance = $income - $expense;


/* LAST 6 MONTHS DATA */
$trend = $conn->query("
SELECT 
    DATE_FORMAT(date,'%Y-%m') ym,
    SUM(amount) total
FROM expenses
GROUP BY ym
ORDER BY ym DESC
LIMIT 6
");

$months = [];
$values = [];

while($t=$trend->fetch_assoc()){
    $months[] = $t['ym'];
    $values[] = $t['total'];
}


/* AVG EXPENSE */
$avg = count($values)>0 ? array_sum($values)/count($values) : 0;


/* LAST MONTH */
$last = $values[0] ?? 0;


/* AI LOGIC */

$msg = "";


/* BALANCE WARNING */
if($balance < $avg){

    $msg .= "⚠️ Low Balance Alert: Current balance may not sustain next month.<br>";

}


/* EXPENSE SPIKE */
if($last > $avg * 1.5){

    $msg .= "📈 Unusual Expense Detected: Last month expenses are much higher than normal.<br>";

}


/* SAVING SUGGESTION */
if($income > $expense){

    $msg .= "💡 Saving Suggestion: You may allocate part of the surplus for future projects.<br>";

}


/* GROWTH STATUS */
if($income < $expense){

    $msg .= "❗ Risk Alert: Expenses exceed income. Budget review is recommended.<br>";

}


/* DEFAULT */
if(empty($msg)){

    $msg = "✅ Financial status is stable. No major risks detected.";

}


echo $msg;