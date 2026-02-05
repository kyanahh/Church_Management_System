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

$id     = $_POST['id'];
$name   = trim($_POST['name']);
$email  = trim($_POST['email']);
$role   = $_POST['role'];
$status = $_POST['status'];

$stmt = $conn->prepare("
UPDATE users
SET name=?, email=?, role=?, status=?
WHERE user_id=?
");

$stmt->bind_param(
    "ssssi",
    $name,
    $email,
    $role,
    $status,
    $id
);

$stmt->execute();

$_SESSION['toast'] = "User updated successfully!";

header("Location: users.php");
exit();