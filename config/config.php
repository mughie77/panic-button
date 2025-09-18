<?php
/*
 * Database Configuration File
 *
 * Please fill in your database credentials below.
 * This file is responsible for connecting to the database.
 */

// --- DATABASE CREDENTIALS ---
// Replace with your database server (e.g., 'localhost' or '127.0.0.1')
define('DB_SERVER', 'db');

// Replace with your database username
define('DB_USERNAME', 'user');

// Replace with your database password
define('DB_PASSWORD', 'password');

// Replace with your database name
define('DB_NAME', 'panic_button_db');


// --- ESTABLISH DATABASE CONNECTION ---
/*
 * Attempt to connect to the MySQL database.
 * The '@' symbol is used to suppress the default PHP warning on connection failure,
 * allowing for a custom error message to be displayed.
 */
$mysqli = @new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    // If connection fails, stop the script and display a user-friendly error message.
    // In a production environment, you might want to log this error instead of displaying it.
    die("Connection failed: " . $mysqli->connect_error);
}

// Set the character set to utf8mb4 for full Unicode support
$mysqli->set_charset("utf8mb4");

?>
