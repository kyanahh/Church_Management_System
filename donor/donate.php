<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='donor'){
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Make a Donation</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">

</head>
<body>

<?php include("includes/navbar.php"); ?>


<div class="container my-5">


<div class="card donate-card shadow">

<div class="card-body p-4">


<h4 class="text-center mb-3">
Support the Church via GCash
</h4>


<p class="text-center text-muted mb-4">
Scan the QR code below and upload your payment proof.
</p>


<!-- GCash QR -->
<div class="text-center mb-4">

<img src="../assets/img/gcash_qr.jpg"
     class="img-fluid rounded shadow"
     style="max-width:220px">

<p class="mt-2 fw-bold text-success">
GCash Number: 09XXXXXXXXX
</p>

</div>


<form action="donate_action.php"
      method="POST"
      enctype="multipart/form-data">


<!-- AMOUNT -->
<div class="mb-3">

<label class="fw-bold">
Donation Amount (PHP)
</label>

<input type="number"
name="amount"
class="form-control"
step="0.01"
min="1"
required>

</div>


<!-- TRANSACTION -->
<div class="mb-3">

<label class="fw-bold">
GCash Reference Number
</label>

<input type="text"
name="txn"
class="form-control"
placeholder="Enter GCash reference"
required>

</div>


<!-- PROOF -->
<div class="mb-3">

<label class="fw-bold">
Upload Payment Proof
</label>

<input type="file"
name="proof"
class="form-control"
accept="image/*"
required>

<small class="text-muted">
(JPG, PNG only)
</small>

</div>


<!-- SUBMIT -->
<div class="d-grid mt-4">

<button type="submit" class="btn btn-gold btn-lg">

<i class="bi bi-heart-fill"></i>
 Submit Donation

</button>

</div>


</form>

</div>

</div>

</div>


<footer class="text-center py-3 text-muted bg-light mt-5">

© <?= date("Y") ?> San Nicolas de Tolentino Parish Church

</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>