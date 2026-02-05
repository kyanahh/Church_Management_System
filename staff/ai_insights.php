<?php
session_start();

if($_SESSION['user']['role']=="staff"){
    $readonly = true;
}

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

if($_SESSION['user']['role']=='donor'){
    header("Location: ../donor/home.php");
    exit();
}

include "../config/db.php";

/* Get Totals */
$don = $conn->query("SELECT SUM(amount) total FROM income")
->fetch_assoc()['total'] ?? 0;

$exp = $conn->query("SELECT SUM(amount) total FROM expenses")
->fetch_assoc()['total'] ?? 0;

$bal = $don - $exp;

/* Monthly Averages */
$avgDon = $conn->query("
SELECT AVG(t) a FROM (
SELECT SUM(amount) t FROM income GROUP BY MONTH(date)
)x
")->fetch_assoc()['a'] ?? 0;

$avgExp = $conn->query("
SELECT AVG(t) a FROM (
SELECT SUM(amount) t FROM expenses GROUP BY MONTH(date)
)x
")->fetch_assoc()['a'] ?? 0;

/* Health Status */
if($bal < 0){
    $status="Critical";
    $color="danger";
}
elseif($exp > $don*0.8){
    $status="Warning";
    $color="warning";
}
else{
    $status="Healthy";
    $color="success";
}

/* Top Expense Category */
$topExp=$conn->query("
SELECT category,SUM(amount) t
FROM expenses
GROUP BY category
ORDER BY t DESC
LIMIT 1
")->fetch_assoc();

/* Forecast */
$forecast = max(0, $avgDon - $avgExp);

?>

<!DOCTYPE html>
<html>
<head>
<title>AI Insights</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>

<?php include("includes/staff_sidebar.php"); ?>

<!-- MAIN -->
<div class="main-content">

<h4 class="text-brown fw-bold mb-4">
AI Financial Insights
</h4>

<!-- STATUS -->
<div class="card shadow-sm mb-4 border-<?= $color ?>">

<div class="card-body text-center">

<h5>Financial Health</h5>

<h3 class="text-<?= $color ?>">
<?= $status ?>
</h3>

<p>
Balance: ₱<?= number_format($bal,2) ?>
</p>

</div>
</div>

<!-- INSIGHTS -->
<div class="row g-3">

<!-- DONATION TREND -->
<div class="col-md-4">

<div class="card shadow-sm h-100">

<div class="card-body">

<h6>Donation Trend</h6>

<p>
Average Monthly Donation:
<br>
<strong>₱<?= number_format($avgDon,2) ?></strong>
</p>

<?php if($avgDon < 5000): ?>
<span class="text-danger">
Low donation trend detected.
</span>
<?php else: ?>
<span class="text-success">
Stable donation flow.
</span>
<?php endif; ?>

</div>
</div>
</div>

<!-- EXPENSE PATTERN -->
<div class="col-md-4">

<div class="card shadow-sm h-100">

<div class="card-body">

<h6>Spending Pattern</h6>

<p>
Highest Expense Category:
<br>
<strong>
<?= $topExp['category'] ?? 'N/A' ?>
</strong>
</p>

<p>
Amount:
₱<?= number_format($topExp['t'] ?? 0,2) ?>
</p>

</div>
</div>
</div>

<!-- FORECAST -->
<div class="col-md-4">

<div class="card shadow-sm h-100">

<div class="card-body">

<h6>Next Month Forecast</h6>

<p>
Expected Balance:
<br>
<strong>
₱<?= number_format($forecast,2) ?>
</strong>
</p>

<?php if($forecast < 2000): ?>
<span class="text-warning">
Risk of shortage next month.
</span>
<?php else: ?>
<span class="text-success">
Projected stable finances.
</span>
<?php endif; ?>

</div>
</div>
</div>

</div>

<!-- RECOMMENDATION -->
<div class="card shadow-sm mt-4">

<div class="card-body">

<h6>AI Recommendation</h6>

<ul>

<?php if($status=="Critical"): ?>
<li>Reduce non-essential expenses immediately.</li>
<li>Launch emergency donation drive.</li>
<li>Postpone large projects.</li>

<?php elseif($status=="Warning"): ?>
<li>Review top expense categories.</li>
<li>Control operational costs.</li>
<li>Increase donor engagement.</li>

<?php else: ?>
<li>Maintain current budget strategy.</li>
<li>Consider expanding outreach.</li>
<li>Save emergency fund.</li>

<?php endif; ?>

<li>Top spending area: <?= $topExp['category'] ?? 'N/A' ?></li>
<li>Monitor OR and receipts regularly.</li>

</ul>

</div>
</div>

</div>

</body>
</html>