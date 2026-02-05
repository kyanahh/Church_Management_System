<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

// Only Admin Allowed
if($_SESSION['user']['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

include "../config/db.php";

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>User Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">

</head>
<body>
    
<?php include("includes/sidebar.php"); ?>

<!-- MAIN -->
<div class="main-content">

<div class="d-flex justify-content-between align-items-center mb-4">

<h4 class="text-brown fw-bold">
    User Management
</h4>

<div class="d-flex gap-2">

<input type="text"
       id="searchUser"
       class="form-control"
       placeholder="Search...">

<button class="btn btn-gold"
        data-bs-toggle="modal"
        data-bs-target="#addUserModal">

<i class="bi bi-plus-circle"></i>

</button>

</div>

</div>

<div class="card shadow-sm">

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-light text-center">
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody class="text-center">

<?php while($row = $users->fetch_assoc()): ?>

<tr>

<td><?= $row['user_id'] ?></td>

<td><?= htmlspecialchars($row['name']) ?></td>

<td><?= htmlspecialchars($row['email']) ?></td>

<td>
    <span class="badge bg-secondary">
        <?= ucfirst($row['role']) ?>
    </span>
</td>

<td>
<?php if($row['status'] == 'active'): ?>
    <span class="badge bg-success">Active</span>
<?php else: ?>
    <span class="badge bg-danger">Inactive</span>
<?php endif; ?>
</td>

<td>

<!-- EDIT -->
<a href="edit_user.php?id=<?= $row['user_id'] ?>"
   class="btn btn-sm btn-primary"
   title="Edit User">

   <i class="bi bi-pencil-square"></i>

</a>

<!-- TOGGLE -->
<a href="user_action.php?action=toggle&id=<?= $row['user_id'] ?>"
   class="btn btn-sm btn-warning"
   title="Activate / Deactivate">

   <i class="bi bi-arrow-repeat"></i>

</a>

<!-- DELETE -->
<button class="btn btn-sm btn-danger"
        data-bs-toggle="modal"
        data-bs-target="#deleteModal"
        data-id="<?= $row['user_id'] ?>"
        data-name="<?= htmlspecialchars($row['name']) ?>">

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

<!-- TOAST -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">

<div id="toastMsg"
     class="toast align-items-center text-white bg-success border-0"
     role="alert">

<div class="d-flex">

<div class="toast-body">
<?= $_SESSION['toast'] ?? '' ?>
</div>

<button type="button"
        class="btn-close btn-close-white me-2 m-auto"
        data-bs-dismiss="toast">
</button>

</div>
</div>

</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal">

<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-brown text-white">
<h5>Add New User</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="add_user.php" method="POST">

<div class="modal-body">

<div class="mb-3">
<label>Full Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label>Role</label>
<select name="role" class="form-select">

<option value="staff">Staff</option>
<option value="donor">Donor</option>

</select>
</div>

</div>

<div class="modal-footer">

<button class="btn btn-secondary" data-bs-dismiss="modal">
Cancel
</button>

<button type="submit" name="addUser" class="btn btn-gold">
Save
</button>

</div>

</form>

</div>
</div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content">

<div class="modal-header bg-danger text-white">

<h5 class="modal-title">
Confirm Delete
</h5>

<button class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body text-center">

<p class="mb-2">
Are you sure you want to delete this user?
</p>

<h6 class="text-danger fw-bold" id="deleteUserName"></h6>

<p class="text-muted mt-2">
This action cannot be undone.
</p>

</div>

<div class="modal-footer">

<form action="user_action.php" method="POST">

<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" id="deleteUserId">

<button type="button"
        class="btn btn-secondary"
        data-bs-dismiss="modal">

Cancel

</button>

<button type="submit"
        class="btn btn-danger">

Delete

</button>

</form>

</div>

</div>
</div>
</div>

<!-- End of modal -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS FOR TOAST -->

<?php if(isset($_SESSION['toast'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function(){
    var toast = new bootstrap.Toast(document.getElementById('toastMsg'));
    toast.show();
});
</script>
<?php unset($_SESSION['toast']); endif; ?>

<script>
document.getElementById("searchUser").addEventListener("keyup", function(){

let filter = this.value.toLowerCase();
let rows = document.querySelectorAll("tbody tr");

rows.forEach(row => {

let text = row.innerText.toLowerCase();

row.style.display = text.includes(filter) ? "" : "none";

});

});
</script>

<script>

const deleteModal = document.getElementById('deleteModal');

deleteModal.addEventListener('show.bs.modal', function (event) {

    const button = event.relatedTarget;

    const userId   = button.getAttribute('data-id');
    const userName = button.getAttribute('data-name');

    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').innerText = userName;

});

</script>


</body>
</html>