<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');
header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => 'Gagal mengambil data guru.'];
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    $stmt = $mysqli->prepare("
        SELECT t.full_name, u.username
        FROM teachers t
        JOIN users u ON t.user_id = u.id
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();

    if ($teacher) {
        $response['success'] = true;
        $response['data'] = $teacher;
        $response['message'] = 'Data guru berhasil diambil.';
    }
}

$mysqli->close();
echo json_encode($response);
?>
