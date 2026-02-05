<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

if($_SESSION['user']['role'] == 'donor'){
    header("Location: ../donor/home.php");
    exit();
}

include "../config/db.php";

// Totals
$don = $conn->query("SELECT SUM(amount) total FROM income")->fetch_assoc();
$exp = $conn->query("SELECT SUM(amount) total FROM expenses")->fetch_assoc();

$totalDon = $don['total'] ?? 0;
$totalExp = $exp['total'] ?? 0;
$balance  = $totalDon - $totalExp;

// Monthly Data
$donData = $conn->query("
SELECT MONTH(date) m, SUM(amount) t
FROM income GROUP BY MONTH(date)
");

$expData = $conn->query("
SELECT MONTH(date) m, SUM(amount) t
FROM expenses GROUP BY MONTH(date)
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>

<?php include("includes/sidebar.php"); ?>

<!-- MAIN CONTENT -->
<div class="main-content">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="text-brown fw-bold">Dashboard</h4>
        <small class="text-muted">
            Welcome, <?= $_SESSION['user']['name'] ?>
        </small>
    </div>

</div>

<!-- SUMMARY CARDS -->
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center">

                <h6>Total Income</h6>
                <h3 class="text-success">
                    ₱<?= number_format($totalDon,2) ?>
                </h3>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center">

                <h6>Total Expenses</h6>
                <h3 class="text-danger">
                    ₱<?= number_format($totalExp,2) ?>
                </h3>

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center">

                <h6>Balance</h6>
                <h3 class="text-primary">
                    ₱<?= number_format($balance,2) ?>
                </h3>

            </div>
        </div>
    </div>

</div>

<!-- CHART + AI -->
<div class="row">

<!-- CHART -->
<div class="col-md-8 mb-3">

    <div class="card shadow-sm">

        <div class="card-body">

            <h6 class="mb-3">Monthly Financial Overview</h6>

            <canvas id="financeChart"></canvas>

        </div>

    </div>

</div>

<!-- AI PANEL -->
<div class="col-md-4 mb-3">

    <div class="card shadow-sm border-warning">

        <div class="card-body">

            <h6 class="text-warning mb-3">
                <i class="bi bi-robot"></i> AI Insights
            </h6>

            <div id="aiBox" class="mb-3">
                Loading...
            </div>

            <button onclick="loadAI()" class="btn btn-gold w-100">
                Refresh Analysis
            </button>

        </div>

    </div>

</div>

</div>

</div>

<!-- CHART SCRIPT -->
<script>

const ctx = document.getElementById('financeChart');

new Chart(ctx, {

type: 'line',

data: {

labels: [1,2,3,4,5,6,7,8,9,10,11,12],

datasets: [

{
label: 'Income',
borderColor: 'green',
fill: false,
data: [

<?php
$arr = array_fill(1,12,0);
while($r=$donData->fetch_assoc()){
    $arr[$r['m']]=$r['t'];
}
foreach($arr as $v){
    echo $v.',';
}
?>

]
},

{
label: 'Expenses',
borderColor: 'red',
fill: false,
data: [

<?php
$arr2 = array_fill(1,12,0);
while($r=$expData->fetch_assoc()){
    $arr2[$r['m']]=$r['t'];
}
foreach($arr2 as $v){
    echo $v.',';
}
?>

]
}

]

}

});

// AI Loader
function loadAI(){

fetch('../ai/get_ai.php')

.then(res => res.text())

.then(data => {

document.getElementById("aiBox").innerHTML = data;

});

}

loadAI();

</script>

</body>
</html>