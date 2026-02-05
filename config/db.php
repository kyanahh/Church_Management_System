<?php
$conn = new mysqli("localhost","root","","church_finance");

if($conn->connect_error){
    die("Connection Failed: ".$conn->connect_error);
}
?>
