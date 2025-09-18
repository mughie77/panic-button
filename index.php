<?php
require_once 'lib/functions.php';
start_session();

// If user is already logged in, redirect to their dashboard
if (is_logged_in()) {
    if ($_SESSION['role'] === 'guru') {
        redirect('admin/dashboard.php');
    } else {
        redirect('student/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panic Button</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card my-5">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Sistem Pelaporan Panik</h3>
                        <h5 class="card-title text-center mb-4">Login</h5>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php
                                    $error = sanitize_input($_GET['error']);
                                    if ($error == 1) {
                                        echo "Username atau password salah.";
                                    } else {
                                        echo "Terjadi kesalahan. Silakan coba lagi.";
                                    }
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="login_process.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
