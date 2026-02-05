<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../auth/login.php");
    exit();
}

require('../fpdf/fpdf.php');
include "../config/db.php";

$id = $_GET['id'] ?? 0;

// Get donation info
$stmt = $conn->prepare("
SELECT d.*, u.name
FROM income d
LEFT JOIN users u ON d.donor_id = u.user_id
WHERE d.donation_id = ?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if(!$data){
    die("Invalid receipt.");
}

// Format date
$date = date("F j, Y", strtotime($data['date']));

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont("Arial","B",16);

// Header
$pdf->Cell(0,10,"San Nicolas de Tolentino Parish Church",0,1,"C");
$pdf->SetFont("Arial","",12);
$pdf->Cell(0,6,"Donation Receipt",0,1,"C");

$pdf->Ln(10);

// Body
$pdf->SetFont("Arial","",11);

$pdf->Cell(50,8,"Reference Code:",0,0);
$pdf->Cell(0,8,$data['ref_code'],0,1);

$pdf->Cell(50,8,"Donor Name:",0,0);
$pdf->Cell(0,8,$data['name'] ?? "Anonymous",0,1);

$pdf->Cell(50,8,"Amount:",0,0);
$pdf->Cell(0,8,"PHP ".number_format($data['amount'],2),0,1);

$pdf->Cell(50,8,"Purpose:",0,0);
$pdf->Cell(0,8,$data['purpose'],0,1);

$pdf->Cell(50,8,"Date:",0,0);
$pdf->Cell(0,8,$date,0,1);

$pdf->Ln(10);

// Footer
$pdf->SetFont("Arial","I",10);

$pdf->MultiCell(0,6,
"This receipt confirms that the above donation was received by the church. Thank you for your generosity and support.");

$pdf->Ln(10);

$pdf->Cell(0,6,"Generated on: ".date("F j, Y h:i A"),0,1,"R");

// Output
$pdf->Output("I","Receipt-".$data['ref_code'].".pdf");