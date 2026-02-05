<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='donor'){
    exit();
}

include "../config/db.php";

$id = $_SESSION['user']['id'];

$name  = trim($_POST['name']);
$email = trim($_POST['email']);

$pass  = $_POST['password'];
$conf  = $_POST['confirm'];


/* VALIDATION */
if(empty($name) || empty($email)){
    header("Location: profile.php?error=empty");
    exit();
}


/* CHECK EMAIL DUPLICATE */
$chk = $conn->prepare("
SELECT user_id FROM users
WHERE email=? AND user_id!=?
");

$chk->bind_param("si",$email,$id);
$chk->execute();

if($chk->get_result()->num_rows > 0){

    header("Location: profile.php?error=email");
    exit();
}


/* UPDATE BASIC INFO */
$stmt = $conn->prepare("
UPDATE users SET name=?, email=?
WHERE user_id=?
");

$stmt->bind_param("ssi",$name,$email,$id);
$stmt->execute();


/* UPDATE PASSWORD (OPTIONAL) */
if(!empty($pass)){

    if($pass != $conf){
        header("Location: profile.php?error=pass");
        exit();
    }

    if(strlen($pass) < 6){
        header("Location: profile.php?error=short");
        exit();
    }

    $hash = password_hash($pass,PASSWORD_DEFAULT);

    $p = $conn->prepare("
    UPDATE users SET password=?
    WHERE user_id=?
    ");

    $p->bind_param("si",$hash,$id);
    $p->execute();
}


/* UPDATE SESSION NAME */
$_SESSION['user']['name'] = $name;


$_SESSION['toast'] = "Profile updated successfully!";
header("Location: profile.php");
exit();