<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

if($_SESSION['user']['role']=='donor'){
    header("Location: ../donor/home.php");
    exit();
}

include "../config/db.php";

// Filters
$month = $_GET['month'] ?? date("m");
$year  = $_GET['year'] ?? date("Y");

// Get totals
$don = $conn->query("
SELECT SUM(amount) total
FROM donations
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
")->fetch_assoc();

$exp = $conn->query("
SELECT SUM(amount) total
FROM expenses
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
")->fetch_assoc();

$totalDon = $don['total'] ?? 0;
$totalExp = $exp['total'] ?? 0;
$balance  = $totalDon - $totalExp;

// Donation List
$donations = $conn->query("
SELECT * FROM donations
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
");

// Expense List
$expenses = $conn->query("
SELECT * FROM expenses
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Financial Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<h4>Church Finance</h4>

<a href="index.php">
<i class="bi bi-speedometer2"></i> Dashboard
</a>

<a href="donations.php">
<i class="bi bi-cash-coin"></i> Donations
</a>

<a href="expenses.php">
<i class="bi bi-wallet2"></i> Expenses
</a>

<a href="reports.php" class="active">
<i class="bi bi-bar-chart"></i> Reports
</a>

<a href="users.php">
<i class="bi bi-people"></i> Users
</a>

<hr style="color:#fff">

<a href="../auth/logout.php">
<i class="bi bi-box-arrow-right"></i> Logout
</a>

</div>

<!-- MAIN -->
<div class="main-content">

<h4 class="text-brown fw-bold mb-3">
Financial Reports
</h4>

<!-- FILTER -->
<form class="row g-2 mb-4">

<div class="col-md-3">

<select name="month" class="form-select">

<?php
for($m=1;$m<=12;$m++){
$selected = ($m==$month)?"selected":"";
echo "<option value='$m' $selected>".date("F",mktime(0,0,0,$m,1))."</option>";
}
?>

</select>

</div>

<div class="col-md-3">

<select name="year" class="form-select">

<?php
for($y=date("Y");$y>=2020;$y--){
$sel = ($y==$year)?"selected":"";
echo "<option $sel>$y</option>";
}
?>

</select>

</div>

<div class="col-md-2">

<button class="btn btn-gold w-100">
Generate
</button>

</div>

<div class="col-md-4 text-end">

<a href="report_pdf.php?month=<?= $month ?>&year=<?= $year ?>"
   target="_blank"
   class="btn btn-success">

<i class="bi bi-file-earmark-pdf"></i> Download PDF

</a>

</div>

</form>

<!-- SUMMARY -->
<div class="row g-3 mb-4">

<div class="col-md-4">
<div class="card shadow-sm text-center">
<div class="card-body">

<h6>Total Donations</h6>
<h4 class="text-success">
₱<?= number_format($totalDon,2) ?>
</h4>

</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow-sm text-center">
<div class="card-body">

<h6>Total Expenses</h6>
<h4 class="text-danger">
₱<?= number_format($totalExp,2) ?>
</h4>

</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow-sm text-center">
<div class="card-body">

<h6>Balance</h6>
<h4 class="text-primary">
₱<?= number_format($balance,2) ?>
</h4>

</div>
</div>
</div>

</div>

<!-- CHART -->
<div class="card shadow-sm mb-4">

<div class="card-body">

<h6>Income vs Expenses</h6>

<canvas id="repChart"></canvas>

</div>

</div>

<!-- TABLES -->
<div class="row">

<!-- DONATIONS -->
<div class="col-md-6">

<div class="card shadow-sm mb-3">

<div class="card-body">

<h6>Donations</h6>

<table class="table table-sm">

<tr>
<th>Ref</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php while($d=$donations->fetch_assoc()): ?>

<tr>
<td><?= $d['ref_code'] ?></td>
<td>₱<?= number_format($d['amount'],2) ?></td>
<td><?= date("M j, Y",strtotime($d['date'])) ?></td>
</tr>

<?php endwhile; ?>

</table>

</div>
</div>

</div>

<!-- EXPENSES -->
<div class="col-md-6">

<div class="card shadow-sm mb-3">

<div class="card-body">

<h6>Expenses</h6>

<table class="table table-sm">

<tr>
<th>Ref</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php while($e=$expenses->fetch_assoc()): ?>

<tr>
<td><?= $e['ref_code'] ?></td>
<td>₱<?= number_format($e['amount'],2) ?></td>
<td><?= date("M j, Y",strtotime($e['date'])) ?></td>
</tr>

<?php endwhile; ?>

</table>

</div>
</div>

</div>

</div>

</div>

<!-- CHART SCRIPT -->
<script>

new Chart(document.getElementById('repChart'),{

type:'bar',

data:{

labels:['Donations','Expenses','Balance'],

datasets:[{

label:'Amount (PHP)',

data:[
<?= $totalDon ?>,
<?= $totalExp ?>,
<?= $balance ?>
],

backgroundColor:[
'green','red','blue'
]

}]

}

});

</script>

</body>
</html>