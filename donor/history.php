<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='donor'){
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$id = $_SESSION['user']['id'];


/* SEARCH */
$search = $_GET['search'] ?? '';

$sql = "
SELECT *
FROM income
WHERE source='$id'
AND type='Donation'
";

if($search){
    $sql .= " AND (ref_code LIKE '%$search%'
              OR transaction_id LIKE '%$search%')";
}

$sql .= " ORDER BY date DESC";

$list = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
<title>Donation History</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">

</head>
<body>

<?php include("includes/navbar.php"); ?>


<!-- MAIN -->
<div class="container my-5">


<div class="d-flex justify-content-between align-items-center mb-3">

<h4 class="fw-bold text-brown">
Donation History
</h4>

<form class="d-flex" method="GET">

<input type="text"
name="search"
class="form-control me-2"
placeholder="Search reference..."
value="<?= htmlspecialchars($search) ?>">

<button class="btn btn-outline-primary">
Search
</button>

</form>

</div>


<div class="card shadow-sm">

<div class="card-body p-0">


<div class="table-responsive">

<table class="table table-bordered table-hover text-center mb-0">


<thead class="table-light">

<tr>
<th>#</th>
<th>Reference</th>
<th>Amount</th>
<th>GCash Ref</th>
<th>Date</th>
<th>Status</th>
<th>Proof</th>
<th>Receipt</th>
</tr>

</thead>


<tbody>


<?php if($list->num_rows==0): ?>

<tr>
<td colspan="8" class="text-muted py-4">
No donation records found.
</td>
</tr>

<?php endif; ?>


<?php $no=1; while($r=$list->fetch_assoc()): ?>


<tr>

<td><?= $no++ ?></td>

<td class="fw-bold text-primary">
<?= $r['ref_code'] ?>
</td>

<td class="text-success fw-bold">
₱<?= number_format($r['amount'],2) ?>
</td>

<td>
<?= htmlspecialchars($r['transaction_id']) ?>
</td>

<td>
<?= date("F j, Y",strtotime($r['date'])) ?>
</td>


<!-- STATUS -->
<td>

<?php if($r['status']=='verified'): ?>

<span class="badge bg-success">
Verified
</span>

<?php elseif($r['status']=='rejected'): ?>

<span class="badge bg-danger">
Rejected
</span>

<?php else: ?>

<span class="badge bg-warning text-dark">
Pending
</span>

<?php endif; ?>

</td>


<!-- PROOF -->
<td>

<?php if(!empty($r['proof'])): ?>

<a href="../uploads/proofs/<?= $r['proof'] ?>"
target="_blank"
class="btn btn-sm btn-info">

<i class="bi bi-image"></i>

</a>

<?php else: ?>

<span class="text-muted">N/A</span>

<?php endif; ?>

</td>


<!-- RECEIPT -->
<td>

<a href="receipt.php?id=<?= $r['income_id'] ?>"
target="_blank"
class="btn btn-sm btn-success">

<i class="bi bi-file-earmark-pdf"></i>

</a>

</td>


</tr>


<?php endwhile; ?>


</tbody>

</table>

</div>

</div>

</div>


<!-- BACK -->
<div class="mt-3">

<a href="home.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i> Back to Home

</a>

</div>


</div>


<footer class="text-center py-3 text-muted bg-light mt-5">

© <?= date("Y") ?> San Nicolas de Tolentino Parish Church

</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>