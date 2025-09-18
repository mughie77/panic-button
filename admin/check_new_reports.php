<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');
header('Content-Type: application/json');

// Get the ID of the last report the client has seen.
// Default to 0 if not provided.
$last_known_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

// Fetch new reports with an ID greater than the last known ID.
$query = "
    SELECT
        r.id,
        r.report_time,
        r.status,
        u.username,
        s.full_name,
        s.class
    FROM reports r
    JOIN users u ON r.student_user_id = u.id
    JOIN students s ON u.id = s.user_id
    WHERE r.id > ?
    ORDER BY r.id ASC
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $last_known_id);
$stmt->execute();
$result = $stmt->get_result();
$new_reports = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$mysqli->close();

echo json_encode(['reports' => $new_reports]);
?>
