<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='donor'){
    exit();
}

include "../config/db.php";


/* DATA */
$amt = $_POST['amount'];
$txn = $_POST['txn'];
$id  = $_SESSION['user']['id'];

$type = "Donation";
$method = "GCash";

$ref = "DON-".date("Ymd")."-".rand(1000,9999);


/* UPLOAD */
$proofName = "";

if(isset($_FILES['proof'])){

    $ext = pathinfo($_FILES['proof']['name'],PATHINFO_EXTENSION);

    $allowed = ['jpg','jpeg','png'];

    if(!in_array(strtolower($ext),$allowed)){

        header("Location: donate.php?error=file");
        exit();
    }

    $proofName = $ref.".".$ext;

    move_uploaded_file(
        $_FILES['proof']['tmp_name'],
        "../uploads/proofs/".$proofName
    );
}


/* SAVE */
$stmt = $conn->prepare("
INSERT INTO income
(ref_code,type,source,payment_method,
transaction_id,proof,amount,date)
VALUES (?,?,?,?,?,?,?,CURDATE())
");

$stmt->bind_param(
"ssssssd",
$ref,$type,$id,$method,$txn,$proofName,$amt
);

$stmt->execute();


header("Location: history.php?success=1");
exit();