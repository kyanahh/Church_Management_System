<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

/* ADD */
if(isset($_POST['add'])){

$cat = $_POST['category'];
$other = $_POST['other_details'] ?? '';
$or = trim($_POST['or_number']);
$amt = $_POST['amount'];
$desc = $_POST['description'];
$date = $_POST['date'];

if($cat=="Others" && empty($other)){
    $_SESSION['toast']="Specify Other Category!";
    header("Location: expenses.php");
    exit();
}

if(empty($or)){
    $_SESSION['toast']="OR Number Required!";
    header("Location: expenses.php");
    exit();
}

// Generate Reference
$ref = "EXP-".date("Ymd")."-".rand(1000,9999);

$stmt=$conn->prepare("
INSERT INTO expenses
(ref_code,category,other_details,or_number,
amount,description,date)
VALUES (?,?,?,?,?,?,?)
");

$stmt->bind_param(
"ssssdds",
$ref,$cat,$other,$or,$amt,$desc,$date
);

$stmt->execute();

$_SESSION['toast']="Expense added! Ref: $ref";
}


/* UPDATE */
if(isset($_POST['update'])){

$id=$_POST['id'];

$cat=$_POST['category'];
$other=$_POST['other_details'];
$or=$_POST['or_number'];
$amt=$_POST['amount'];
$desc=$_POST['description'];
$date=$_POST['date'];

if($cat=="Others" && empty($other)){
    $_SESSION['toast']="Specify Other Category!";
    header("Location: expenses.php");
    exit();
}

$stmt=$conn->prepare("
UPDATE expenses SET
category=?,other_details=?,or_number=?,
amount=?,description=?,date=?
WHERE expense_id=?
");

$stmt->bind_param(
"sssddsi",
$cat,$other,$or,$amt,$desc,$date,$id
);

$stmt->execute();

$_SESSION['toast']="Expense updated!";
}


/* DELETE */
if(isset($_POST['delete'])){

$id=$_POST['id'];

$conn->query("DELETE FROM expenses WHERE expense_id='$id'");

$_SESSION['toast']="Expense deleted!";
}

header("Location: expenses.php");
exit();