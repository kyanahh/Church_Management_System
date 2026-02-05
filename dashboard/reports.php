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

/* FILTERS */
$month = $_GET['month'] ?? date("m");
$year  = $_GET['year'] ?? date("Y");


/* MONTHLY TOTALS */
$inc = $conn->query("
SELECT SUM(amount) total
FROM income
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
")->fetch_assoc()['total'] ?? 0;

$exp = $conn->query("
SELECT SUM(amount) total
FROM expenses
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
")->fetch_assoc()['total'] ?? 0;

$balance = $inc - $exp;


/* LISTS */
$income = $conn->query("
SELECT * FROM income
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
ORDER BY date DESC
");

$expenses = $conn->query("
SELECT * FROM expenses
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
ORDER BY date DESC
");


/* ANNUAL COMPARISON */
$incCompare = $conn->query("
SELECT YEAR(date) yr, SUM(amount) total
FROM income
GROUP BY YEAR(date)
ORDER BY yr
");

$expCompare = $conn->query("
SELECT YEAR(date) yr, SUM(amount) total
FROM expenses
GROUP BY YEAR(date)
ORDER BY yr
");

$incArr=[];
while($i=$incCompare->fetch_assoc()){
$incArr[$i['yr']]=$i['total'];
}

$expArr=[];
while($e=$expCompare->fetch_assoc()){
$expArr[$e['yr']]=$e['total'];
}

$years = array_unique(
array_merge(array_keys($incArr),array_keys($expArr))
);

sort($years);

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

<?php include("includes/sidebar.php"); ?>


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
$sel = ($m==$month)?"selected":"";
echo "<option value='$m' $sel>".date("F",mktime(0,0,0,$m,1))."</option>";
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

<a href="financial_statement_pdf.php?year=<?= $year ?>"
target="_blank"
class="btn btn-primary">

<i class="bi bi-file-earmark-pdf"></i>
 Annual Statement

</a>

</div>

</form>


<!-- SUMMARY -->
<div class="row g-3 mb-4">

<div class="col-md-4">
<div class="card shadow-sm text-center">
<div class="card-body">

<h6>Total Income</h6>
<h4 class="text-success">
₱<?= number_format($inc,2) ?>
</h4>

</div>
</div>
</div>


<div class="col-md-4">
<div class="card shadow-sm text-center">
<div class="card-body">

<h6>Total Expenses</h6>
<h4 class="text-danger">
₱<?= number_format($exp,2) ?>
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


<!-- MONTHLY CHART -->
<div class="card shadow-sm mb-4">

<div class="card-body">

<h6>Monthly Summary</h6>

<canvas id="repChart"></canvas>

</div>
</div>


<!-- ANNUAL COMPARISON -->
<div class="card shadow-sm mb-4">

<div class="card-body">

<h6>Annual Comparison</h6>

<table class="table table-bordered text-center">

<tr>
<th>Year</th>
<th>Income</th>
<th>Expenses</th>
<th>Balance</th>
</tr>

<?php foreach($years as $y):

$in = $incArr[$y] ?? 0;
$ex = $expArr[$y] ?? 0;
$bal = $in-$ex;

?>

<tr>

<td><?= $y ?></td>
<td>₱<?= number_format($in,2) ?></td>
<td>₱<?= number_format($ex,2) ?></td>
<td>₱<?= number_format($bal,2) ?></td>

</tr>

<?php endforeach; ?>

</table>

</div>
</div>


<!-- TABLES -->
<div class="row">


<!-- INCOME -->
<div class="col-md-6">

<div class="card shadow-sm mb-3 report-card">

<div class="card-body">

<h6>Income Records</h6>

<table class="table table-sm">

<tr>
<th>Ref</th>
<th>Type</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php while($d=$income->fetch_assoc()): ?>

<tr>

<td><?= $d['ref_code'] ?></td>
<td><?= $d['type'] ?></td>
<td>₱<?= number_format($d['amount'],2) ?></td>
<td><?= date("M j, Y",strtotime($d['date'])) ?></td>

</tr>

<?php endwhile; ?>

</table>

</div>
</div>
</div>


<!-- EXPENSE -->
<div class="col-md-6">

<div class="card shadow-sm mb-3 report-card">

<div class="card-body">

<h6>Expense Records</h6>

<table class="table table-sm">

<tr>
<th>Ref</th>
<th>Category</th>
<th>Amount</th>
<th>Date</th>
</tr>

<?php while($e=$expenses->fetch_assoc()): ?>

<tr>

<td><?= $e['ref_code'] ?></td>
<td><?= $e['category'] ?></td>
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

labels:['Income','Expenses','Balance'],

datasets:[{

label:'Amount (PHP)',

data:[
<?= $inc ?>,
<?= $exp ?>,
<?= $balance ?>
],

backgroundColor:[
'#198754',
'#dc3545',
'#0d6efd'
]

}]

}

});

</script>

</body>
</html>