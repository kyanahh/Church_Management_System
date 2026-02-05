<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

// Only Admin
if($_SESSION['user']['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

include "../config/db.php";

$action = $_GET['action'] ?? '';
$id     = $_GET['id'] ?? 0;


// Toggle Status
if($action == 'toggle'){

    // Get current status
    $q = $conn->query("SELECT status FROM users WHERE user_id='$id'");
    $u = $q->fetch_assoc();

    $newStatus = ($u['status'] == 'active') ? 'inactive' : 'active';

    $conn->query("
        UPDATE users
        SET status='$newStatus'
        WHERE user_id='$id'
    ");

    $_SESSION['toast'] = "User status updated!";

}

if($id == $_SESSION['user']['id']){
    header("Location: users.php");
    exit();
}


// Delete User
if($action == 'delete'){

    $conn->query("DELETE FROM users WHERE user_id='$id'");

    $_SESSION['toast'] = "User deleted!";

}

header("Location: users.php");
exit();