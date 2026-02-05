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

// Get expenses
$expenses = $conn->query("
SELECT * FROM expenses
ORDER BY expense_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Expense Management</title>

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

<h4 class="text-brown fw-bold">Expense Management</h4>

<div class="d-flex gap-2 col-sm-5">

<input type="text"
       id="searchExpense"
       class="form-control"
       placeholder="Search by ref, OR, category...">

<button class="btn btn-gold"
        data-bs-toggle="modal"
        data-bs-target="#addExpenseModal">

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
<th>Category</th>
<th>OR No.</th>
<th>Amount</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>

<tbody class="text-center">

<?php while($row=$expenses->fetch_assoc()): ?>

<tr>

<td><?= $row['expense_id'] ?></td>

<td><?= htmlspecialchars($row['ref_code']) ?></td>

<td>
<?= htmlspecialchars($row['category']) ?>
<?php if($row['category']=="Others"): ?>
<br><small>(<?= htmlspecialchars($row['other_details']) ?>)</small>
<?php endif; ?>
</td>

<td><?= htmlspecialchars($row['or_number']) ?></td>

<td>₱<?= number_format($row['amount'],2) ?></td>

<td>
<?= date("F j, Y", strtotime($row['date'])) ?>
</td>

<td>

<!-- EDIT -->
<button class="btn btn-sm btn-primary editBtn"

data-id="<?= $row['expense_id'] ?>"
data-ref="<?= $row['ref_code'] ?>"
data-cat="<?= $row['category'] ?>"
data-other="<?= $row['other_details'] ?>"
data-or="<?= $row['or_number'] ?>"
data-amt="<?= $row['amount'] ?>"
data-desc="<?= $row['description'] ?>"
data-date="<?= $row['date'] ?>"

data-bs-toggle="modal"
data-bs-target="#editExpenseModal">

<i class="bi bi-pencil-square"></i>

</button>

<!-- DELETE -->
<button class="btn btn-sm btn-danger"

data-id="<?= $row['expense_id'] ?>"

data-bs-toggle="modal"
data-bs-target="#deleteExpenseModal">

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

<!-- ADD EXPENSE MODAL -->
<div class="modal fade" id="addExpenseModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-brown text-white">
<h5>Add Expense</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="expense_action.php" method="POST">

<div class="modal-body">

<div class="mb-3">
<label>Category</label>
<select name="category" id="addCategory"
        class="form-select" required>

<option value="">Select</option>
<option>Utilities</option>
<option>Office Supplies</option>
<option>Maintenance</option>
<option>Outreach</option>
<option>Equipment</option>
<option>Transportation</option>
<option>Charity</option>
<option>Others</option>

</select>
</div>

<div class="mb-3 d-none" id="addOtherBox">
<label>Specify (Others)</label>
<input type="text" name="other_details"
       class="form-control">
</div>

<div class="mb-3">
<label>OR Number</label>
<input type="text" name="or_number"
       class="form-control" required>
</div>

<div class="mb-3">
<label>Amount</label>
<input type="number" step="0.01"
       name="amount"
       class="form-control" required>
</div>

<div class="mb-3">
<label>Description</label>
<input type="text"
       name="description"
       class="form-control">
</div>

<div class="mb-3">
<label>Date</label>
<input type="date"
       name="date"
       class="form-control" required>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button type="submit" name="add"
        class="btn btn-gold">
Save
</button>

</div>

</form>

</div>
</div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editExpenseModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-primary text-white">
<h5>Edit Expense</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="expense_action.php" method="POST">

<input type="hidden" name="id" id="editId">

<div class="modal-body">

<div class="mb-3">
<label>Reference</label>
<input type="text" id="editRef"
class="form-control" readonly>
</div>

<div class="mb-3">
<label>Category</label>
<select name="category" id="editCategory"
class="form-select">

<option>Utilities</option>
<option>Office Supplies</option>
<option>Maintenance</option>
<option>Outreach</option>
<option>Equipment</option>
<option>Transportation</option>
<option>Charity</option>
<option>Others</option>

</select>
</div>

<div class="mb-3 d-none" id="editOtherBox">
<label>Specify (Others)</label>
<input type="text" name="other_details"
id="editOther" class="form-control">
</div>

<div class="mb-3">
<label>OR Number</label>
<input type="text" name="or_number"
id="editOr" class="form-control" required>
</div>

<div class="mb-3">
<label>Amount</label>
<input type="number" step="0.01"
name="amount" id="editAmt"
class="form-control" required>
</div>

<div class="mb-3">
<label>Description</label>
<input type="text"
name="description" id="editDesc"
class="form-control">
</div>

<div class="mb-3">
<label>Date</label>
<input type="date"
name="date" id="editDate"
class="form-control" required>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary"
data-bs-dismiss="modal">Cancel</button>

<button type="submit"
name="update"
class="btn btn-primary">Update</button>

</div>

</form>

</div>
</div>
</div>


<!-- DELETE MODAL -->
<div class="modal fade" id="deleteExpenseModal">

<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-danger text-white">
<h5>Confirm Delete</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-footer">

<form action="expense_action.php" method="POST">

<input type="hidden" name="id" id="deleteId">

<button class="btn btn-secondary"
data-bs-dismiss="modal">Cancel</button>

<button type="submit"
name="delete"
class="btn btn-danger">Delete</button>

</form>

</div>

</div>
</div>
</div>

<!-- Scripts -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

// SEARCH
document.getElementById("searchExpense").addEventListener("keyup",function(){

let f=this.value.toLowerCase();

document.querySelectorAll("tbody tr").forEach(r=>{
r.style.display=r.innerText.toLowerCase().includes(f)?"":"none";
});

});

// SHOW OTHERS (ADD)
document.getElementById("addCategory").onchange=function(){

document.getElementById("addOtherBox")
.classList.toggle("d-none",this.value!=="Others");

};

// EDIT
document.querySelectorAll(".editBtn").forEach(btn=>{

btn.onclick=()=>{

editId.value=btn.dataset.id;
editRef.value=btn.dataset.ref;
editCategory.value=btn.dataset.cat;
editOther.value=btn.dataset.other;
editOr.value=btn.dataset.or;
editAmt.value=btn.dataset.amt;
editDesc.value=btn.dataset.desc;
editDate.value=btn.dataset.date;

editOtherBox.classList
.toggle("d-none",btn.dataset.cat!=="Others");

};

});

// SHOW OTHERS (EDIT)
editCategory.onchange=function(){

editOtherBox.classList
.toggle("d-none",this.value!=="Others");

};

// DELETE
document.querySelectorAll("[data-bs-target='#deleteExpenseModal']")
.forEach(btn=>{

btn.onclick=()=>{

deleteId.value=btn.dataset.id;

};

});

<?php if(isset($_SESSION['toast'])): ?>
var toast=new bootstrap.Toast(document.getElementById("toastMsg"));
toast.show();
<?php unset($_SESSION['toast']); endif; ?>

</script>

</body>
</html>