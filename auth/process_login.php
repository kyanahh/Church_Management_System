<?php

session_start();
include "../config/db.php";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare query (secure)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows === 1){

        $user = $result->fetch_assoc();

        // Verify password
        if(password_verify($password, $user['password']) 
            && $user['status'] == 'active'){


            $_SESSION['user'] = [
                'id' => $user['user_id'],
                'name' => $user['name'],
                'role' => $user['role']
            ];

            header("Location: ../dashboard/index.php");
            exit();

        }
    }

    header("Location: login.php?error=1");
    exit();

}