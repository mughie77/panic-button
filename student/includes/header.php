<?php
// This file provides the header and navigation for the student section.
require_once '../lib/functions.php';
require_once '../config/config.php';
require_role('siswa');

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/student.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet"> <!-- Original styles for panic button etc. -->
</head>
<body>
    <!-- Mobile-only Toggler Button -->
    <button class="btn btn-primary d-md-none sidebar-toggler" type="button" id="sidebarToggle">
        &#9776; <!-- Hamburger Icon -->
    </button>

    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
            <h4 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Siswa Panel</span>
            </h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                        Dashboard
                    </a>
                </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Akun</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <span class="nav-link text-muted"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
