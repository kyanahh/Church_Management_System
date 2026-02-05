<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='donor'){
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

$id = $_SESSION['user']['id'];


/* GET USER DATA */
$stmt = $conn->prepare("
SELECT name,email,status
FROM users
WHERE user_id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">

</head>
<body>

<?php include("includes/navbar.php"); ?>


<div class="container my-5" style="max-width:700px">


<h4 class="fw-bold text-brown mb-4 text-center">
My Profile
</h4>


<div class="card shadow-sm">

<div class="card-body p-4">


<!-- BASIC INFO -->
<h6 class="mb-3 text-muted">
Account Information
</h6>

<form action="update_profile.php" method="POST">


<div class="mb-3">

<label>Full Name</label>

<input type="text"
name="name"
class="form-control"
value="<?= htmlspecialchars($user['name']) ?>"
required>

</div>


<div class="mb-3">

<label>Email</label>

<input type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($user['email']) ?>"
required>

</div>


<div class="mb-3">

<label>Status</label>

<input type="text"
class="form-control"
value="<?= ucfirst($user['status']) ?>"
disabled>

</div>


<hr>


<!-- PASSWORD -->
<h6 class="mb-3 text-muted">
Change Password
</h6>


<div class="mb-3">

<label>New Password</label>

<input type="password"
name="password"
class="form-control"
placeholder="Leave blank if no change">

</div>


<div class="mb-3">

<label>Confirm Password</label>

<input type="password"
name="confirm"
class="form-control"
placeholder="Confirm password">

</div>


<div class="d-grid mt-4">

<button class="btn btn-gold btn-lg">

<i class="bi bi-save"></i>
 Save Changes

</button>

</div>


</form>

</div>
</div>

</div>


<footer class="text-center py-3 text-muted bg-light mt-5">

© <?= date("Y") ?> San Nicolas de Tolentino Parish Church

</footer>

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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if(isset($_SESSION['toast'])): ?>

<script>
document.addEventListener("DOMContentLoaded", function(){

    var toastEl = document.getElementById('toastMsg');

    var toast = new bootstrap.Toast(toastEl,{
        delay: 3000   // 3 seconds
    });

    toast.show();

});
</script>

<?php unset($_SESSION['toast']); endif; ?>

</body>
</html>