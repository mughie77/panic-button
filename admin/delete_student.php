<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    // We only need to delete from the 'users' table.
    // The ON DELETE CASCADE constraint will automatically delete the corresponding row in the 'students' table.
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'siswa'");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Deletion successful
        redirect('manage_students.php?success=delete');
    } else {
        // Deletion failed
        redirect('manage_students.php?error=delete');
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Invalid ID
    redirect('manage_students.php');
}
?>
