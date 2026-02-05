<?php

session_start();
include "../config/db.php";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Secure query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();


    if($result->num_rows === 1){

        $user = $result->fetch_assoc();


        // Verify password + status
        if(
            password_verify($password, $user['password']) &&
            $user['status'] === 'active'
        ){

            // Save session
            $_SESSION['user'] = [

                'id'   => $user['user_id'],
                'name' => $user['name'],
                'role' => $user['role']

            ];


            /* ROLE REDIRECTION */
            if($user['role'] === 'admin'){

                header("Location: ../dashboard/index.php");
                exit();

            }
            elseif($user['role'] === 'staff'){

                header("Location: ../dashboard/staff_index.php");
                exit();

            }
            elseif($user['role'] === 'donor'){

                header("Location: ../donor/home.php");
                exit();

            }
            else{

                // Unknown role (safety)
                session_destroy();
                header("Location: login.php?error=role");
                exit();

            }

        }

    }

    // Invalid login
    header("Location: login.php?error=1");
    exit();

}