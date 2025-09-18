<?php
// This file provides the header and navigation for the admin section.
require_once '../lib/functions.php';
require_once '../config/config.php'; // Need config for db access on pages
require_role('guru');

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Styles -->
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link href="../assets/css/elegant.css" rel="stylesheet">
</head>
<body>
    <!-- Mobile-only Toggler Button -->
    <button class="btn btn-primary d-md-none sidebar-toggler" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
        <div class="position-sticky pt-3">
            <h4 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                <span>Admin Panel</span>
            </h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'beranda.php') ? 'active' : ''; ?>" href="beranda.php">
                        <i class="bi bi-house-door"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a id="laporanLink" class="nav-link <?php echo ($current_page == 'laporan.php') ? 'active' : ''; ?>" href="laporan.php" style="position: relative;">
                        <i class="bi bi-table"></i> Daftar Laporan
                        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle" style="display: none;">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'manage_students.php') ? 'active' : ''; ?>" href="manage_students.php">
                        <i class="bi bi-people"></i> Manajemen Siswa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'manage_teachers.php') ? 'active' : ''; ?>" href="manage_teachers.php">
                        <i class="bi bi-person-video3"></i> Manajemen Guru
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>" href="manage_users.php">
                        <i class="bi bi-person-gear"></i> Manajemen User
                    </a>
                </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                <span>Akun</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <span class="nav-link"><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
