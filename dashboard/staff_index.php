<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

if($_SESSION['user']['role']!='staff'){
    header("Location: index.php");
    exit();
}

include "../config/db.php";


/* TOTALS */
$inc = $conn->query("SELECT SUM(amount) total FROM income")->fetch_assoc()['total'] ?? 0;

$exp = $conn->query("SELECT SUM(amount) total FROM expenses")->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html>
<head>
<title>Staff Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>


<?php include("includes/staff_sidebar.php"); ?>


<div class="main-content">

<h4 class="fw-bold text-brown mb-3">
Staff Dashboard
</h4>

<p class="text-muted mb-4">
Welcome, <?= $_SESSION['user']['name'] ?>
</p>


<!-- SUMMARY -->
<div class="row g-3 mb-4">

<div class="col-md-6">
<div class="card shadow-sm text-center">

<div class="card-body">

<h6>Total Income</h6>
<h4 class="text-success">
₱<?= number_format($inc,2) ?>
</h4>

</div>
</div>
</div>


<div class="col-md-6">
<div class="card shadow-sm text-center">

<div class="card-body">

<h6>Total Expenses</h6>
<h4 class="text-danger">
₱<?= number_format($exp,2) ?>
</h4>

</div>
</div>
</div>

</div>


<!-- QUICK ACTIONS -->
<div class="card shadow-sm">

<div class="card-body">

<h6 class="mb-3">Quick Actions</h6>

<div class="d-grid gap-2">

<a href="income.php" class="btn btn-outline-success">
<i class="bi bi-cash"></i> Add Income
</a>

<a href="expenses.php" class="btn btn-outline-danger">
<i class="bi bi-wallet2"></i> Add Expense
</a>

<a href="reports.php" class="btn btn-outline-primary">
<i class="bi bi-bar-chart"></i> View Reports
</a>

</div>

</div>
</div>

</div>

</body>
</html>