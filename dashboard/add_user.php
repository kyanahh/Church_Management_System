<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='admin'){
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

if(isset($_POST['addUser'])){

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$email = strtolower($email);
$pass = $_POST['password'];
$role = $_POST['role'];

$hash = password_hash($pass, PASSWORD_DEFAULT);

// Check duplicate
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0){

    $_SESSION['toast'] = "Email already exists!";
    header("Location: users.php");
    exit();
}

// Insert
$stmt = $conn->prepare("
INSERT INTO users (name,email,password,role,status)
VALUES (?,?,?,?, 'active')
");

$stmt->bind_param("ssss",$name,$email,$hash,$role);

$stmt->execute();

$_SESSION['toast'] = "User added successfully!";
header("Location: users.php");
exit();

}