<?php

include "../config/db.php";

$don = $conn->query("SELECT SUM(amount) total FROM donations")->fetch_assoc()['total'] ?? 0;
$exp = $conn->query("SELECT SUM(amount) total FROM expenses")->fetch_assoc()['total'] ?? 0;

$balance = $don - $exp;

// Simple AI Logic (Rule-Based)

if($exp > $don * 0.8){

    echo "⚠️ Expenses are high. Consider reducing costs.";

}
elseif($balance < 10000){

    echo "💡 Funds are low. Increase donation campaigns.";

}
else{

    echo "✅ Financial status is healthy. Continue operations.";

}