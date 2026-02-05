<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

// Admin only
if($_SESSION['user']['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

include "../config/db.php";

// Validate ID
$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user){
    header("Location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">

<h4>Church Finance</h4>

<a href="index.php">
<i class="bi bi-speedometer2"></i> Dashboard
</a>

<a href="users.php" class="active">
<i class="bi bi-people"></i> Users
</a>

<hr style="color:#fff">

<a href="../auth/logout.php">
<i class="bi bi-box-arrow-right"></i> Logout
</a>

</div>

<!-- MAIN -->
<div class="main-content">

<h4 class="text-brown fw-bold mb-4">
Edit User
</h4>

<div class="card shadow-sm">

<div class="card-body">

<form action="update_user.php" method="POST">

<input type="hidden" name="id" value="<?= $user['user_id'] ?>">

<div class="mb-3">
<label class="form-label">Full Name</label>
<input type="text"
       name="name"
       class="form-control"
       value="<?= htmlspecialchars($user['name']) ?>"
       required>
</div>

<div class="mb-3">
<label class="form-label">Email</label>
<input type="email"
       name="email"
       class="form-control"
       value="<?= htmlspecialchars($user['email']) ?>"
       required>
</div>

<div class="mb-3">
<label class="form-label">Role</label>

<select name="role" class="form-select">

<option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>
Admin
</option>

<option value="staff" <?= $user['role']=='staff'?'selected':'' ?>>
Staff
</option>

<option value="donor" <?= $user['role']=='donor'?'selected':'' ?>>
Donor
</option>

</select>
</div>

<div class="mb-3">
<label class="form-label">Status</label>

<select name="status" class="form-select">

<option value="active" <?= $user['status']=='active'?'selected':'' ?>>
Active
</option>

<option value="inactive" <?= $user['status']=='inactive'?'selected':'' ?>>
Inactive
</option>

</select>
</div>

<div class="d-flex gap-2">

<button type="submit"
        class="btn btn-gold">

<i class="bi bi-save"></i> Save Changes

</button>

<a href="users.php"
   class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>
</div>

</div>

</body>
</html>