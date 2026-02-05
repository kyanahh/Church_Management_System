<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['role']!='donor'){
    exit();
}

require('../fpdf/fpdf.php');
include "../config/db.php";


$id = $_GET['id'] ?? 0;

$id = intval($id);


/* GET DONATION */
$stmt = $conn->prepare("
SELECT i.*, u.name
FROM income i
JOIN users u ON i.source = u.user_id
WHERE i.income_id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$d = $stmt->get_result()->fetch_assoc();

if(!$d){
    exit();
}


/* CREATE PDF */
$pdf = new FPDF();
$pdf->AddPage();


/* HEADER */
$pdf->SetFont("Arial","B",18);
$pdf->Cell(0,10,"San Nicolas de Tolentino Parish Church",0,1,"C");

$pdf->SetFont("Arial","",12);
$pdf->Cell(0,7,"Cupang, Muntinlupa City",0,1,"C");

$pdf->Ln(5);

$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,"OFFICIAL DONATION RECEIPT",0,1,"C");

$pdf->Ln(8);


/* RECEIPT INFO */
$pdf->SetFont("Arial","",11);

$pdf->Cell(0,7,"Receipt No: ".$d['ref_code'],0,1);
$pdf->Cell(0,7,"Donor Name: ".$d['name'],0,1);
$pdf->Cell(0,7,"Payment Method: ".$d['payment_method'],0,1);
$pdf->Cell(0,7,"Transaction Ref: ".$d['transaction_id'],0,1);
$pdf->Cell(0,7,"Date: ".date("F j, Y",strtotime($d['date'])),0,1);

$pdf->Ln(8);


/* TABLE */
$pdf->SetFont("Arial","B",12);
$pdf->SetFillColor(230,230,230);

$pdf->Cell(120,10,"Description",1,0,"C",true);
$pdf->Cell(70,10,"Amount (PHP)",1,1,"C",true);


$pdf->SetFont("Arial","",12);

$pdf->Cell(120,10,"Church Donation",1);
$pdf->Cell(70,10,number_format($d['amount'],2),1,1,"R");


$pdf->SetFont("Arial","B",12);

$pdf->Cell(120,10,"Total",1);
$pdf->Cell(70,10,number_format($d['amount'],2),1,1,"R");


$pdf->Ln(15);


/* MESSAGE */
$pdf->SetFont("Arial","",11);

$pdf->MultiCell(0,7,
"Thank you for your generous contribution. Your donation helps support church programs, outreach activities, and community services."
);

$pdf->Ln(12);


/* SIGNATURE */
$pdf->Cell(0,6,"Prepared by:",0,1);

$pdf->Ln(12);

$pdf->SetFont("Arial","B",11);
$pdf->Cell(0,6,"____________________________",0,1);
$pdf->Cell(0,6,"Church Treasurer / Administrator",0,1);


$pdf->Ln(10);


/* FOOTER */
$pdf->SetY(-30);

$pdf->SetFont("Arial","I",9);

$pdf->Cell(0,6,"Generated on ".date("F j, Y h:i A"),0,1,"C");

$pdf->Cell(0,6,"This is a system-generated receipt and is valid without signature.",0,1,"C");


/* OUTPUT */
$pdf->Output("I","Donation-Receipt-".$d['ref_code'].".pdf");