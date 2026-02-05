<?php
session_start();

if(!isset($_SESSION['user'])){
    exit();
}

require('../fpdf/fpdf.php');
include "../config/db.php";

$year = $_GET['year'] ?? date("Y");


/* GET TOTALS */
$inc = $conn->query("
SELECT SUM(amount) total
FROM income
WHERE YEAR(date)='$year'
")->fetch_assoc()['total'] ?? 0;

$exp = $conn->query("
SELECT SUM(amount) total
FROM expenses
WHERE YEAR(date)='$year'
")->fetch_assoc()['total'] ?? 0;

$net = $inc - $exp;


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
$pdf->Cell(0,10,"ANNUAL FINANCIAL STATEMENT",0,1,"C");

$pdf->SetFont("Arial","",11);
$pdf->Cell(0,6,"For the Year $year",0,1,"C");

$pdf->Ln(10);


/* TABLE HEADER */
$pdf->SetFont("Arial","B",12);
$pdf->SetFillColor(230,230,230);

$pdf->Cell(90,10,"Description",1,0,"C",true);
$pdf->Cell(90,10,"Amount (PHP)",1,1,"C",true);


/* TABLE DATA */
$pdf->SetFont("Arial","",12);

$pdf->Cell(90,10,"Total Income",1);
$pdf->Cell(90,10,number_format($inc,2),1,1,"R");

$pdf->Cell(90,10,"Total Expenses",1);
$pdf->Cell(90,10,number_format($exp,2),1,1,"R");

$pdf->SetFont("Arial","B",12);

$pdf->Cell(90,10,"Net Balance",1);
$pdf->Cell(90,10,number_format($net,2),1,1,"R");


$pdf->Ln(15);


/* SUMMARY */
$pdf->SetFont("Arial","",11);

$pdf->MultiCell(0,7,
"This report presents the summarized financial performance of San Nicolas de Tolentino Parish Church for the year $year. It includes all recorded income and expenses based on the official financial management system."
);

$pdf->Ln(15);


/* SIGNATURE */
$pdf->Cell(0,6,"Prepared by:",0,1);

$pdf->Ln(15);

$pdf->SetFont("Arial","B",11);
$pdf->Cell(0,6,"____________________________",0,1);
$pdf->Cell(0,6,"Church Treasurer / Administrator",0,1);


$pdf->Ln(10);


/* FOOTER */
$pdf->SetY(-40);

$pdf->SetFont("Arial","I",9);

$pdf->Cell(0,6,"Generated on ".date("F j, Y h:i A"),0,1,"C");

$pdf->Cell(0,6,"This document is system-generated and officially recorded.",0,1,"C");


/* OUTPUT */
$pdf->Output("I","Financial-Statement-$year.pdf");