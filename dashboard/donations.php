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

// Get donations
$donations = $conn->query("
SELECT d.*, u.name
FROM donations d
LEFT JOIN users u ON d.donor_id = u.user_id
ORDER BY d.donation_id DESC
");

// Get donors
$donors = $conn->query("SELECT user_id,name FROM users WHERE role='donor' AND status='active'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Donation Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>

<?php include("includes/sidebar.php"); ?>

<!-- MAIN -->
<div class="main-content">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

<h4 class="text-brown fw-bold">Donation Management</h4>

<div class="d-flex gap-2 col-sm-5">

<input type="text"
       id="searchDonation"
       class="form-control"
       placeholder="Search by donor, purpose, or reference...">

<button class="btn btn-gold"
        data-bs-toggle="modal"
        data-bs-target="#addDonationModal">

<i class="bi bi-plus-circle"></i>

</button>

</div>

</div>

<!-- TABLE -->
<div class="card shadow-sm">

<div class="card-body">

<table class="table table-hover table-bordered">

<thead class="table-light text-center">
<tr>
<th>ID</th>
<th>Reference</th>
<th>Donor</th>
<th>Amount</th>
<th>Purpose</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>

<tbody class="text-center">

<?php while($row=$donations->fetch_assoc()): ?>

<tr>

<td><?= $row['donation_id'] ?></td>
<td><?= htmlspecialchars($row['ref_code']) ?></td>

<td><?= htmlspecialchars($row['name'] ?? 'Anonymous') ?></td>

<td>₱<?= number_format($row['amount'],2) ?></td>

<td><?= htmlspecialchars($row['purpose']) ?></td>

<td>
<?= date("F j, Y", strtotime($row['date'])) ?>
</td>

<td>

<a href="receipt.php?id=<?= $row['donation_id'] ?>"
   target="_blank"
   class="btn btn-sm btn-success"
   title="Download Receipt">

<i class="bi bi-file-earmark-pdf"></i>

</a>

<!-- EDIT -->
<button class="btn btn-sm btn-primary editBtn"
        data-id="<?= $row['donation_id'] ?>"
        data-donor="<?= $row['donor_id'] ?>"
        data-amount="<?= $row['amount'] ?>"
        data-purpose="<?= htmlspecialchars($row['purpose']) ?>"
        data-date="<?= $row['date'] ?>"
        data-bs-toggle="modal"
        data-bs-target="#editDonationModal">

<i class="bi bi-pencil-square"></i>

</button>

<!-- DELETE -->
<button class="btn btn-sm btn-danger"
        data-id="<?= $row['donation_id'] ?>"
        data-ref="<?= $row['ref_code'] ?>"
        data-bs-toggle="modal"
        data-bs-target="#deleteDonationModal">

<i class="bi bi-trash"></i>

</button>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>
</div>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addDonationModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-brown text-white">
<h5>Add Donation</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="donation_action.php" method="POST">

<div class="modal-body">

<div class="mb-3">
<label>Donor</label>
<select name="donor_id" class="form-select">

<option value="">Anonymous</option>

<?php while($d=$donors->fetch_assoc()): ?>
<option value="<?= $d['user_id'] ?>">
<?= htmlspecialchars($d['name']) ?>
</option>
<?php endwhile; ?>

</select>
</div>

<div class="mb-3">
<label>Amount</label>
<input type="number" step="0.01" name="amount" class="form-control" required>
</div>

<div class="mb-3">
<label>Purpose</label>
<select name="purpose" class="form-select" required>
<option value="">Select Purpose</option>
<option>Tithes</option>
<option>Offerings</option>
<option>Building Fund</option>
<option>Outreach</option>
<option>Charity</option>
<option>Others</option>
</select>
</div>

<div class="mb-3">
<label>Date</label>
<input type="date" name="date" class="form-control" required>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

<button type="submit" name="add" class="btn btn-gold">
Save
</button>

</div>

</form>

</div>
</div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editDonationModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-primary text-white">
<h5>Edit Donation</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="donation_action.php" method="POST">

<input type="hidden" name="id" id="editId">

<div class="modal-body">

<div class="mb-3">
<label>Reference Code</label>
<input type="text"
       id="editRef"
       class="form-control"
       readonly>
</div>

<div class="mb-3">
<label>Amount</label>
<input type="number" step="0.01" name="amount" id="editAmount"
class="form-control" required>
</div>

<div class="mb-3">
<label>Purpose</label>
<input type="text" name="purpose" id="editPurpose"
class="form-control" required>
</div>

<div class="mb-3">
<label>Date</label>
<input type="date" name="date" id="editDate"
class="form-control" required>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

<button type="submit" name="update" class="btn btn-primary">
Update
</button>

</div>

</form>

</div>
</div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteDonationModal">

<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-danger text-white">
<h5>Confirm Delete</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body text-center">

<p>Delete this donation record?</p>

</div>

<div class="modal-footer">

<form action="donation_action.php" method="POST">

<input type="hidden" name="id" id="deleteId">

<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

<button type="submit" name="delete" class="btn btn-danger">
Delete
</button>

</form>

</div>

</div>
</div>
</div>

<!-- TOAST -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">

<div id="toastMsg"
     class="toast bg-success text-white">

<div class="toast-body">
<?= $_SESSION['toast'] ?? '' ?>
</div>

</div>

</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Search
document.getElementById("searchDonation").addEventListener("keyup", function(){

let filter = this.value.toLowerCase();
let rows = document.querySelectorAll("tbody tr");

rows.forEach(r=>{
r.style.display = r.innerText.toLowerCase().includes(filter)?"":"none";
});

});

// Edit
document.querySelectorAll(".editBtn").forEach(btn=>{

btn.onclick = ()=>{

document.getElementById("editId").value = btn.dataset.id;
document.getElementById("editRef").value = btn.dataset.ref;
document.getElementById("editAmount").value = btn.dataset.amount;
document.getElementById("editPurpose").value = btn.dataset.purpose;
document.getElementById("editDate").value = btn.dataset.date;

};

});

// Delete
document.querySelectorAll("[data-bs-target='#deleteDonationModal']").forEach(btn=>{

btn.onclick = ()=>{
document.getElementById("deleteId").value = btn.dataset.id;
};

});

<?php if(isset($_SESSION['toast'])): ?>
var toast = new bootstrap.Toast(document.getElementById('toastMsg'));
toast.show();
<?php unset($_SESSION['toast']); endif; ?>

</script>

</body>
</html>