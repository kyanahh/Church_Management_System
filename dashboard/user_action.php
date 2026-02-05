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

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id     = $_POST['id'] ?? $_GET['id'] ?? 0;

$id = intval($id);


/* PREVENT SELF ACTION */
if($id == $_SESSION['user']['id']){
    $_SESSION['toast'] = "You cannot modify your own account.";
    header("Location: users.php");
    exit();
}


/* CHECK USER EXISTS */
$check = $conn->prepare("SELECT role FROM users WHERE user_id=?");
$check->bind_param("i",$id);
$check->execute();
$res = $check->get_result();
$user = $res->fetch_assoc();

if(!$user){
    $_SESSION['toast'] = "User not found.";
    header("Location: users.php");
    exit();
}


/* PREVENT DELETING LAST ADMIN */
if($action=="delete" && $user['role']=="admin"){

    $cnt = $conn->query("
    SELECT COUNT(*) c FROM users WHERE role='admin'
    ")->fetch_assoc()['c'];

    if($cnt <= 1){

        $_SESSION['toast']="Cannot delete the last admin.";
        header("Location: users.php");
        exit();
    }
}


/* TOGGLE STATUS */
if($action=="toggle"){

    $q = $conn->prepare("
    SELECT status FROM users WHERE user_id=?
    ");
    $q->bind_param("i",$id);
    $q->execute();

    $cur = $q->get_result()->fetch_assoc()['status'];

    $new = ($cur=="active")?"inactive":"active";

    $u = $conn->prepare("
    UPDATE users SET status=? WHERE user_id=?
    ");
    $u->bind_param("si",$new,$id);
    $u->execute();

    $_SESSION['toast']="User status updated!";
}


/* DELETE USER */
if($action=="delete"){

    $d = $conn->prepare("
    DELETE FROM users WHERE user_id=?
    ");
    $d->bind_param("i",$id);
    $d->execute();

    $_SESSION['toast']="User deleted!";
}


header("Location: users.php");
exit();