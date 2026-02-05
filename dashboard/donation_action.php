<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

include "../config/db.php";

// ADD
if(isset($_POST['add'])){

    $donor = $_POST['donor_id'] ?: NULL;
    $amt   = $_POST['amount'];
    $pur   = $_POST['purpose'];
    $date  = $_POST['date'];

    // Generate Reference Code
    $ref = "DON-" . date("Ymd") . "-" . rand(1000,9999);

    $stmt = $conn->prepare("
        INSERT INTO donations (ref_code, donor_id, amount, purpose, date)
        VALUES (?,?,?,?,?)
    ");

    $stmt->bind_param("sidss", $ref, $donor, $amt, $pur, $date);
    $stmt->execute();

    $_SESSION['toast'] = "Donation added! Ref: $ref";
}

// UPDATE
if(isset($_POST['update'])){

    $id   = $_POST['id'];
    $amt  = $_POST['amount'];
    $pur  = $_POST['purpose'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("
        UPDATE donations
        SET amount=?, purpose=?, date=?
        WHERE donation_id=?
    ");

    $stmt->bind_param("dssi",$amt,$pur,$date,$id);
    $stmt->execute();

    $_SESSION['toast']="Donation updated!";
}

// DELETE
if(isset($_POST['delete'])){

$id=$_POST['id'];

$conn->query("DELETE FROM donations WHERE donation_id='$id'");

$_SESSION['toast']="Donation deleted!";
}

header("Location: donations.php");
exit();