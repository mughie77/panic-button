<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    $errors = [];
    if (empty($full_name)) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($username)) $errors[] = 'Username wajib diisi.';
    if (empty($password)) $errors[] = 'Password wajib diisi.';

    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Username sudah digunakan.';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'guru';

        $mysqli->begin_transaction();
        try {
            $stmt_user = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt_user->bind_param("sss", $username, $hashed_password, $role);
            $stmt_user->execute();
            $user_id = $mysqli->insert_id;

            $stmt_teacher = $mysqli->prepare("INSERT INTO teachers (user_id, full_name) VALUES (?, ?)");
            $stmt_teacher->bind_param("is", $user_id, $full_name);
            $stmt_teacher->execute();
            $teacher_id = $mysqli->insert_id;

            $mysqli->commit();

            $response['success'] = true;
            $response['message'] = 'Guru baru berhasil ditambahkan!';
            $response['data'] = [
                'user_id' => $user_id,
                'teacher_id' => $teacher_id,
                'full_name' => $full_name,
                'username' => $username
            ];

        } catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();
            $response['message'] = 'Gagal menyimpan data ke database.';
        }
    } else {
        $response['message'] = implode(' ', $errors);
    }
}

$mysqli->close();
echo json_encode($response);
?>
