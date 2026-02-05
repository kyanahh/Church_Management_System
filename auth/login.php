<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: ../dashboard/index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Church Finance | Login</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-brown navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="login.php">
            Church Finance System
        </a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow-lg border-0 rounded-4">

                <div class="card-body p-4">

                    <h3 class="text-center text-brown fw-bold mb-2">
                        Login
                    </h3>

                    <p class="text-center text-muted mb-4">
                        Secure Access Portal
                    </p>

                    <!-- ERROR MESSAGE -->
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger text-center">
                            Invalid email or password
                        </div>
                    <?php endif; ?>

                    <!-- SUCCESS MESSAGE -->
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success text-center">
                            Registration successful! Please login.
                        </div>
                    <?php endif; ?>

                    <form action="process_login.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label text-brown">
                                Email Address
                            </label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-brown">
                                Password
                            </label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit"
                                    name="login"
                                    class="btn btn-gold btn-lg">
                                Login
                            </button>
                        </div>

                    </form>

                    <hr>

                    <p class="text-center mt-3">
                        Don't have an account?
                        <a href="register.php"
                           class="text-decoration-none fw-bold text-brown">
                            Register Here
                        </a>
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>