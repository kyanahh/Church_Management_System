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

// Get income
$income = $conn->query("
SELECT i.*,
       u.name AS donor_name
FROM income i
LEFT JOIN users u
ON i.source = u.user_id
ORDER BY i.income_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Income Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>

<?php include("includes/sidebar.php"); ?>

<!-- MAIN -->
<div class="main-content">

<div class="d-flex justify-content-between mb-3">

<h4 class="fw-bold text-brown">
Income Management
</h4>

<div class="d-flex gap-2 col-sm-5">

<input type="text"
id="searchIncome"
class="form-control"
placeholder="Search ref, type, source...">

<button class="btn btn-gold"
data-bs-toggle="modal"
data-bs-target="#addIncomeModal">

<i class="bi bi-plus-circle"></i>

</button>

</div>

</div>


<!-- TABLE -->
<div class="card shadow-sm">

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-light text-center">
<tr>
<th>ID</th>
<th>Reference</th>
<th>Income Type</th>
<th>From</th>
<th>Payment</th>
<th>Amount</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody class="text-center">

<?php while($row=$income->fetch_assoc()): ?>

<tr>

<td><?= $row['income_id'] ?></td>

<td><?= $row['ref_code'] ?></td>

<td><?= $row['type'] ?></td>

<td>

<?php

// If donor record
if(is_numeric($row['source']) && !empty($row['donor_name'])){

    echo htmlspecialchars($row['donor_name']);

}

// If Anonymous / Others / Manual input
else{

    echo htmlspecialchars($row['source']);

}

?>

</td>

<td><?= $row['payment_method'] ?></td>

<td>₱<?= number_format($row['amount'],2) ?></td>

<td><?= date("F j, Y",strtotime($row['date'])) ?></td>

<td>

<?php if($row['status']=='verified'): ?>
<span class="badge bg-success">Verified</span>
<?php elseif($row['status']=='rejected'): ?>
<span class="badge bg-danger">Rejected</span>
<?php else: ?>
<span class="badge bg-warning text-dark">Pending</span>
<?php endif; ?>

</td>

<td>

<!-- VIEW -->
<button class="btn btn-sm btn-info viewBtn"

data-ref="<?= $row['ref_code'] ?>"
data-type="<?= $row['type'] ?>"
data-source="<?= $row['source'] ?>"
data-source-name="<?= $row['donor_name'] ?>"
data-method="<?= $row['payment_method'] ?>"
data-txn="<?= $row['transaction_id'] ?>"
data-amt="<?= $row['amount'] ?>"
data-rem="<?= $row['remarks'] ?>"
data-status="<?= $row['status'] ?>"
data-comment="<?= $row['admin_comment'] ?>"
data-reviewed="<?= $row['reviewed_at'] ?>"
data-date="<?= $row['date'] ?>"
data-file="<?= $row['proof'] ?>"

data-bs-toggle="modal"
data-bs-target="#viewIncomeModal">

<i class="bi bi-eye"></i>

</button>

<!-- EDIT -->
<button class="btn btn-sm btn-primary editBtn"

data-id="<?= $row['income_id'] ?>"
data-ref="<?= $row['ref_code'] ?>"
data-type="<?= $row['type'] ?>"
data-source="<?= $row['source'] ?>"
data-method="<?= $row['payment_method'] ?>"
data-txn="<?= $row['transaction_id'] ?>"
data-amt="<?= $row['amount'] ?>"
data-rem="<?= $row['remarks'] ?>"
data-date="<?= $row['date'] ?>"

data-bs-toggle="modal"
data-bs-target="#editIncomeModal">

<i class="bi bi-pencil"></i>

</button>

<!-- VERIFY -->
<?php if($_SESSION['user']['role']=="admin" && $row['status']=="pending"): ?>

<button class="btn btn-sm btn-success verifyBtn"
data-id="<?= $row['income_id'] ?>"
data-bs-toggle="modal"
data-bs-target="#reviewModal">

<i class="bi bi-check-circle"></i>

</button>

<button class="btn btn-sm btn-danger rejectBtn"
data-id="<?= $row['income_id'] ?>"
data-bs-toggle="modal"
data-bs-target="#reviewModal">

<i class="bi bi-x-circle"></i>

</button>

<?php endif; ?>


<!-- DELETE -->
<?php if($_SESSION['user']['role']=="admin"): ?>

<button class="btn btn-sm btn-danger"

data-id="<?= $row['income_id'] ?>"

data-bs-toggle="modal"
data-bs-target="#deleteIncomeModal">

<i class="bi bi-trash"></i>

</button>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>
</div>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addIncomeModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-brown text-white">
<h5>Add Income</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="income_action.php" method="POST" enctype="multipart/form-data">

<div class="modal-body">

<div class="mb-2">
<label>Type</label>
<select name="type" class="form-select" required>

<option>Donation</option>
<option>Wedding</option>
<option>Baptism</option>
<option>Mass Collection</option>
<option>Offering</option>
<option>Others</option>

</select>
</div>

<div class="mb-2">
<label>Source / Name</label>
<select name="source_type" id="addSourceType"
class="form-select">

<option value="named">With Name/Source</option>
<option value="others">Others</option>
<option value="anonymous">Anonymous</option>

</select>
</div>

<div class="mb-2" id="addSourceBox">
<input type="text"
name="source"
class="form-control"
placeholder="Enter name/source">
</div>

<div class="mb-2">
<label>Upload Receipt</label>
<input type="file"
name="receipt"
class="form-control"
accept=".jpg,.jpeg,.png,.pdf">
</div>

<div class="mb-2">
<label>Payment Method</label>
<select name="method" class="form-select">

<option>Cash</option>
<option>GCash</option>
<option>Bank</option>
<option>PayMaya</option>

</select>
</div>

<div class="mb-2">
<label>Transaction No. (Online)</label>
<input type="text" name="txn"
class="form-control">
</div>

<div class="mb-2">
<label>Amount</label>
<input type="number" step="0.01"
name="amount" class="form-control" required>
</div>

<div class="mb-2">
<label>Remarks</label>
<input type="text" name="remarks"
class="form-control">
</div>

<div class="mb-2">
<label>Date</label>
<input type="date" name="date"
class="form-control" required>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary"
data-bs-dismiss="modal">Cancel</button>

<button type="submit" name="add"
class="btn btn-gold">Save</button>

</div>

</form>

</div>
</div>
</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewIncomeModal">

<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header bg-info text-white">
<h5>Income Details</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<table class="table table-bordered">

<tr><th>Reference</th><td id="vRef"></td></tr>
<tr><th>Type</th><td id="vType"></td></tr>
<tr><th>Source</th><td id="vSource"></td></tr>
<tr><th>Payment</th><td id="vMethod"></td></tr>
<tr><th>Transaction No.</th><td id="vTxn"></td></tr>
<tr><th>Amount</th><td id="vAmt"></td></tr>
<tr><th>Date</th><td id="vDate"></td></tr>
<tr><th>Remarks</th><td id="vRem"></td></tr>
<tr><th>Status</th><td id="vStatus"></td></tr>
<tr><th>Admin Comment</th><td id="vComment"></td></tr>
<tr><th>Reviewed At</th><td id="vReviewed"></td></tr>

</table>

<div id="vReceipt"></div>

</div>

</div>
</div>
</div>

<!-- EDIT INCOME MODAL -->
<div class="modal fade" id="editIncomeModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-primary text-white">
<h5>Edit Income</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="income_action.php" method="POST">

<input type="hidden" name="id" id="editId">

<div class="modal-body">

<div class="mb-2">
<label>Reference Code</label>
<input type="text" id="editRef"
class="form-control" readonly>
</div>

<div class="mb-2">
<label>Type</label>
<select name="type" id="editType"
class="form-select" required>

<option>Donation</option>
<option>Wedding</option>
<option>Baptism</option>
<option>Mass Collection</option>
<option>Offering</option>
<option>Others</option>

</select>
</div>

<div class="mb-2">
<label>Source / Name</label>
<input type="text" name="source"
id="editSource"
class="form-control">
</div>

<div class="mb-2">
<label>Payment Method</label>
<select name="method"
id="editMethod"
class="form-select">

<option>Cash</option>
<option>GCash</option>
<option>Bank</option>
<option>PayMaya</option>

</select>
</div>

<div class="mb-2">
<label>Transaction ID</label>
<input type="text"
name="txn"
id="editTxn"
class="form-control">
</div>

<div class="mb-2">
<label>Amount</label>
<input type="number" step="0.01"
name="amount"
id="editAmt"
class="form-control" required>
</div>

<div class="mb-2">
<label>Remarks</label>
<input type="text"
name="remarks"
id="editRem"
class="form-control">
</div>

<div class="mb-2">
<label>Date</label>
<input type="date"
name="date"
id="editDate"
class="form-control" required>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary"
data-bs-dismiss="modal">Cancel</button>

<button type="submit"
name="update"
class="btn btn-primary">
Update
</button>

</div>

</form>

</div>
</div>
</div>

<!-- DELETE INCOME MODAL -->
<div class="modal fade" id="deleteIncomeModal">

<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-danger text-white">
<h5>Confirm Delete</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body text-center">

<p>Are you sure you want to delete this income record?</p>
<p class="text-muted">This action cannot be undone.</p>

</div>

<div class="modal-footer">

<form action="income_action.php" method="POST">

<input type="hidden" name="id" id="deleteId">

<button class="btn btn-secondary"
data-bs-dismiss="modal">
Cancel
</button>

<button type="submit"
name="delete"
class="btn btn-danger">
Delete
</button>

</form>

</div>

</div>
</div>
</div>

<!-- REVIEW MODAL -->
<div class="modal fade" id="reviewModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-dark text-white">
<h5>Review Income</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="income_action.php" method="POST">

<input type="hidden" name="review_id" id="reviewId">
<input type="hidden" name="review_status" id="reviewStatus">

<div class="modal-body">

<div class="mb-3">
<label>Admin Comment</label>

<textarea name="comment"
class="form-control"
required
placeholder="Enter reason / notes..."></textarea>

</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary"
data-bs-dismiss="modal">
Cancel
</button>

<button type="submit"
name="review"
class="btn btn-primary">
Submit Review
</button>

</div>

</form>

</div>
</div>
</div>

<!-- TOAST -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">

<div id="toastMsg"
     class="toast align-items-center text-bg-success border-0"
     role="alert">

<div class="d-flex">

<div class="toast-body">

<?= $_SESSION['toast'] ?? '' ?>

</div>

<button type="button"
class="btn-close btn-close-white me-2 m-auto"
data-bs-dismiss="toast"></button>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

<?php if(isset($_SESSION['toast'])): ?>

var toast = new bootstrap.Toast(
document.getElementById('toastMsg'),
{ delay: 3000 }
);

toast.show();

<?php unset($_SESSION['toast']); endif; ?>

</script>

<script>

// Add Source
addSourceType.onchange = function(){

if(this.value=="anonymous"){
    addSourceBox.classList.add("d-none");
}
else{
    addSourceBox.classList.remove("d-none");
}

};

// SEARCH
searchIncome.onkeyup=()=>{

let f=searchIncome.value.toLowerCase();

document.querySelectorAll("tbody tr").forEach(r=>{
r.style.display=r.innerText.toLowerCase().includes(f)?"":"none";
});

};

// EDIT
document.querySelectorAll(".editBtn").forEach(btn=>{

btn.onclick=()=>{

editId.value=btn.dataset.id;
editRef.value=btn.dataset.ref;
editType.value=btn.dataset.type;
editSource.value=btn.dataset.source;
editMethod.value=btn.dataset.method;
editTxn.value=btn.dataset.txn;
editAmt.value=btn.dataset.amt;
editRem.value=btn.dataset.rem;
editDate.value=btn.dataset.date;

};

});

// VIEW MODAL

document.querySelectorAll(".viewBtn").forEach(btn=>{

btn.onclick=()=>{

vRef.innerText   = btn.dataset.ref;
vType.innerText  = btn.dataset.type;
vSource.innerText =
btn.dataset.sourceName ?? btn.dataset.source;vMethod.innerText= btn.dataset.method;
vTxn.innerText   = btn.dataset.txn;
vAmt.innerText   = "₱"+btn.dataset.amt;
vDate.innerText  = btn.dataset.date;
vRem.innerText   = btn.dataset.rem;
vStatus.innerText   = btn.dataset.status;
vComment.innerText  = btn.dataset.comment || "N/A";
vReviewed.innerText = btn.dataset.reviewed || "N/A";


if(btn.dataset.file && btn.dataset.file!=""){

vReceipt.innerHTML =
`<a href="../uploads/proofs/${btn.dataset.file}"
   target="_blank"
   class="btn btn-success">

   <i class="bi bi-image"></i> View Proof

</a>`;

}else{

vReceipt.innerHTML =
'<span class="text-muted">No proof uploaded.</span>';

}

};

});

// REVIEW

document.querySelectorAll(".verifyBtn").forEach(btn=>{

btn.onclick = ()=>{

reviewId.value = btn.dataset.id;
reviewStatus.value = "verified";

};

});


document.querySelectorAll(".rejectBtn").forEach(btn=>{

btn.onclick = ()=>{

reviewId.value = btn.dataset.id;
reviewStatus.value = "rejected";

};

});


</script>

</body>
</html>