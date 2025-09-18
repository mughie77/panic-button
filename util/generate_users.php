<?php
// This is a command-line script to create initial admin and student users.
// Run this script from your terminal: php util/generate_users.php

// We are outside the web root, so paths need to be adjusted.
require_once dirname(__DIR__) . '/config/config.php';

echo "--- Script Inisialisasi Pengguna ---\n";

// --- Create Admin User ---
echo "Membuat akun Admin (guru)...\n";
$admin_username = 'admin';
$admin_fullname = 'Admin Utama';
$admin_password = 'password'; // Default password, user should change it.
$admin_role = 'guru';
$admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);

// Use a transaction
$mysqli->begin_transaction();
try {
    // Check if admin already exists
    $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $admin_username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Akun admin '{$admin_username}' sudah ada. Melewati pembuatan admin.\n";
        $mysqli->rollback(); // No need to proceed with this transaction
    } else {
        // Insert into users table
        $stmt_user = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt_user->bind_param("sss", $admin_username, $admin_hash, $admin_role);
        $stmt_user->execute();
        $user_id = $mysqli->insert_id;

        // Insert into teachers table
        $stmt_teacher = $mysqli->prepare("INSERT INTO teachers (user_id, full_name) VALUES (?, ?)");
        $stmt_teacher->bind_param("is", $user_id, $admin_fullname);
        $stmt_teacher->execute();

        $mysqli->commit();
        echo "Akun Admin berhasil dibuat!\n";
        echo "  Username: " . $admin_username . "\n";
        echo "  Password: " . $admin_password . "\n";
    }
    $check_stmt->close();

} catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    echo "Error creating admin user: " . $exception->getMessage() . "\n";
}


// --- Create Student User ---
echo "\nMembuat akun Siswa (student)...\n";
$student_username = 'siswa';
$student_fullname = 'Siswa Ujicoba';
$student_class = '10A';
$student_password = 'password'; // Default password
$student_role = 'siswa';
$student_hash = password_hash($student_password, PASSWORD_DEFAULT);

// Use a transaction
$mysqli->begin_transaction();
try {
    // Check if student already exists
    $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $student_username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Akun siswa '{$student_username}' sudah ada. Melewati pembuatan siswa.\n";
        $mysqli->rollback();
    } else {
        // Insert into users table
        $stmt_user = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt_user->bind_param("sss", $student_username, $student_hash, $student_role);
        $stmt_user->execute();
        $user_id = $mysqli->insert_id;

        // Insert into students table
        $stmt_student = $mysqli->prepare("INSERT INTO students (user_id, full_name, class) VALUES (?, ?, ?)");
        $stmt_student->bind_param("iss", $user_id, $student_fullname, $student_class);
        $stmt_student->execute();

        $mysqli->commit();
        echo "Akun Siswa berhasil dibuat!\n";
        echo "  Username: " . $student_username . "\n";
        echo "  Password: " . $student_password . "\n";
    }
    $check_stmt->close();

} catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    echo "Error creating student user: " . $exception->getMessage() . "\n";
}

echo "\n--- Selesai ---\n";
$mysqli->close();
?>
