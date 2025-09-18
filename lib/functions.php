<?php
/*
 * Core Functions Library
 *
 * This file contains helper functions used across the application.
 */

/**
 * Starts a session if one has not already been started.
 * This is a safe way to ensure session_start() is only called once.
 */
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Checks if a user is currently logged in.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirects the user to a specified URL.
 *
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Sanitizes user input to prevent Cross-Site Scripting (XSS) attacks.
 *
 * @param string $data The input data to sanitize.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * Checks if the logged-in user has a specific role.
 * If no user is logged in, or the role does not match, it redirects to the login page.
 *
 * @param string $required_role The role required to access the page (e.g., 'siswa' or 'guru').
 */
function require_role($required_role) {
    start_session();
    if (!is_logged_in() || $_SESSION['role'] !== $required_role) {
        // If the user is not logged in or does not have the correct role,
        // destroy the session and redirect to the login page.
        session_destroy();
        redirect('index.php');
    }
}

?>
