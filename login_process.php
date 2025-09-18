<?php
require_once 'config/config.php';
require_once 'lib/functions.php';

start_session();

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    // Prepare a statement to prevent SQL Injection
    $stmt = $mysqli->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, so start a new session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect user to the appropriate dashboard
            if ($role === 'guru') {
                redirect('admin/dashboard.php');
            } else {
                redirect('student/dashboard.php');
            }
        } else {
            // Password is not valid
            redirect('index.php?error=1');
        }
    } else {
        // Username does not exist
        redirect('index.php?error=1');
    }

    $stmt->close();
    $mysqli->close();
} else {
    // If not a POST request, redirect to login page
    redirect('index.php');
}
?>
