<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prevent an admin from deleting themselves
if ($user_id > 0 && $user_id != $_SESSION['user_id']) {
    // We only need to delete from the 'users' table.
    // The ON DELETE CASCADE constraint will automatically delete the corresponding row in the 'teachers' table.
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'guru'");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Deletion successful
        redirect('manage_teachers.php?success=delete');
    } else {
        // Deletion failed
        redirect('manage_teachers.php?error=delete');
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Invalid ID or trying to delete self
    redirect('manage_teachers.php');
}
?>
