<?php

session_start();
include "../config/db.php";

if(isset($_POST['register'])){

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];
    $role     = 'donor';

    // Validate passwords
    if($password !== $confirm){
        header("Location: register.php?error=Passwords do not match");
        exit();
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        header("Location: register.php?error=Email already registered");
        exit();
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("
        INSERT INTO users (name,email,password,role)
        VALUES (?,?,?,?)
    ");

    $stmt->bind_param("ssss",$name,$email,$hashed,$role);

    if($stmt->execute()){
        header("Location: login.php?success=1");
        exit();
    }

    header("Location: register.php?error=Registration failed");
    exit();
}