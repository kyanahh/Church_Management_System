<?php
session_start();

if(!isset($_SESSION['user'])){
    exit();
}

include "../config/db.php";


/* ADD */
if(isset($_POST['add'])){

$type=$_POST['type'];
$typeSrc = $_POST['source_type'];

if($typeSrc=="anonymous"){
    $src="Anonymous";
}
else{
    $src=trim($_POST['source']);
}
$met=$_POST['method'];
$txn=$_POST['txn'];
$amt=$_POST['amount'];
$rem=$_POST['remarks'];
$date=$_POST['date'];

$ref="INC-".date("Ymd")."-".rand(1000,9999);

$stmt=$conn->prepare("
INSERT INTO income
(ref_code,type,source,payment_method,
transaction_id,amount,remarks,date,receipt_file)
VALUES (?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
"sssssdsss",
$ref,$type,$src,$met,$txn,$amt,$rem,$date,$receipt
);

$stmt->execute();

$_SESSION['toast']="Income added! Ref: $ref";
}


/* UPDATE */
if(isset($_POST['update'])){

$id=$_POST['id'];

$type=$_POST['type'];
$src=$_POST['source'];
$met=$_POST['method'];
$txn=$_POST['txn'];
$amt=$_POST['amount'];
$rem=$_POST['remarks'];
$date=$_POST['date'];

$stmt=$conn->prepare("
UPDATE income SET
type=?,source=?,payment_method=?,
transaction_id=?,amount=?,remarks=?,date=?
WHERE income_id=?
");

$stmt->bind_param(
"ssssdssi",
$type,$src,$met,$txn,$amt,$rem,$date,$id
);

$stmt->execute();

$_SESSION['toast']="Income updated!";
}


/* DELETE */
if(isset($_POST['delete'])){

$id=$_POST['id'];

$conn->query("DELETE FROM income WHERE income_id='$id'");

$_SESSION['toast']="Income deleted!";
}

header("Location: income.php");
exit();