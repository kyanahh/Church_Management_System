<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='donor'){
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$id = $_SESSION['user']['id'];


/* TOTAL DONATION */
$q = $conn->query("
SELECT SUM(amount) total
FROM income
WHERE source='$id' AND type='Donation'
");

$total = $q->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Donor Home</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>
    
<?php include("includes/navbar.php"); ?>



<!-- HERO -->
<section class="donor-hero">

<div class="container">

<h1>
San Nicolas de Tolentino Parish
</h1>

<p>
Thank you for your generosity and continued support
</p>

<div class="d-flex justify-content-center gap-3">

<a href="donate.php" class="btn btn-gold btn-lg">
<i class="bi bi-heart-fill"></i> Donate Now
</a>

<a href="history.php" class="btn btn-outline-light btn-lg">
<i class="bi bi-clock-history"></i> History
</a>

</div>

</div>

</section>


<!-- INFO SECTION -->
<div class="container my-5">

<div class="row g-4 justify-content-center">


<!-- TOTAL -->
<div class="col-md-4">

<div class="card donor-card text-center shadow-sm">

<div class="card-body m-5">

<h6 >Total Donated</h6>

<h3 class="text-success fw-bold">
₱<?= number_format($total,2) ?>
</h3>

</div>

</div>

</div>


<!-- DONATE -->
<div class="col-md-4">

<a href="donate.php"
class="text-decoration-none text-dark">

<div class="card donor-card text-center shadow-sm">

<div class="card-body">

<i class="bi bi-cash-coin fs-1 text-warning"></i>

<h6 class="mt-2">Make Donation</h6>

<p class="text-muted small">
Support church programs
</p>

</div>

</div>

</a>

</div>


<!-- HISTORY -->
<div class="col-md-4">

<a href="history.php"
class="text-decoration-none text-dark">

<div class="card donor-card text-center shadow-sm">

<div class="card-body">

<i class="bi bi-file-earmark-text fs-1 text-primary"></i>

<h6 class="mt-2">Donation Records</h6>

<p class="text-muted small">
View your receipts
</p>

</div>

</div>

</a>

</div>


</div>
</div>


<!-- FOOTER -->
<footer class="text-center py-3 text-muted bg-light">

© <?= date("Y") ?> San Nicolas de Tolentino Parish Church

</footer>


</body>
</html>