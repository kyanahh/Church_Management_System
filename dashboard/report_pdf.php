<?php
session_start();

if(!isset($_SESSION['user'])){
    exit();
}

require('../fpdf/fpdf.php');
include "../config/db.php";

$month=$_GET['month'];
$year=$_GET['year'];

$don=$conn->query("
SELECT SUM(amount) total FROM income
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
")->fetch_assoc()['total'] ?? 0;

$exp=$conn->query("
SELECT SUM(amount) total FROM expenses
WHERE MONTH(date)='$month' AND YEAR(date)='$year'
")->fetch_assoc()['total'] ?? 0;

$bal=$don-$exp;

$pdf=new FPDF();
$pdf->AddPage();

$pdf->SetFont("Arial","B",16);

$pdf->Cell(0,10,"San Nicolas de Tolentino Parish Church",0,1,"C");
$pdf->Cell(0,10,"Financial Report",0,1,"C");

$pdf->Ln(5);

$pdf->SetFont("Arial","",12);

$pdf->Cell(0,8,"Period: ".date("F",mktime(0,0,0,$month,1))." $year",0,1);

$pdf->Ln(5);

$pdf->Cell(0,8,"Total Income: PHP ".number_format($don,2),0,1);
$pdf->Cell(0,8,"Total Expenses: PHP ".number_format($exp,2),0,1);
$pdf->Cell(0,8,"Balance: PHP ".number_format($bal,2),0,1);

$pdf->Ln(10);

$pdf->MultiCell(0,6,
"This report summarizes the financial activities of the church for the selected period.");

$pdf->Ln(10);

$pdf->Cell(0,6,"Generated on: ".date("F j, Y h:i A"),0,1,"R");

$pdf->Output("I","Report-$month-$year.pdf");