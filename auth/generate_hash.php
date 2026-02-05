<?php

$password = "1234"; // change to your password

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password <br>";
echo "Hash: $hash";

?>