<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $full_name = sanitize_input($_POST['full_name']);
    $class = sanitize_input($_POST['class']);
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    $errors = [];
    if ($user_id <= 0) $errors[] = 'ID Siswa tidak valid.';
    if (empty($full_name)) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($class)) $errors[] = 'Kelas wajib diisi.';
    if (empty($username)) $errors[] = 'Username wajib diisi.';

    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Username sudah digunakan.';
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $mysqli->begin_transaction();
        try {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt_user = $mysqli->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
                $stmt_user->bind_param("ssi", $username, $hashed_password, $user_id);
            } else {
                $stmt_user = $mysqli->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmt_user->bind_param("si", $username, $user_id);
            }
            $stmt_user->execute();

            $stmt_student = $mysqli->prepare("UPDATE students SET full_name = ?, class = ? WHERE user_id = ?");
            $stmt_student->bind_param("ssi", $full_name, $class, $user_id);
            $stmt_student->execute();

            $mysqli->commit();

            $response['success'] = true;
            $response['message'] = 'Data siswa berhasil diperbarui!';
            $response['data'] = [
                'user_id' => $user_id,
                'full_name' => $full_name,
                'class' => $class,
                'username' => $username
            ];

        } catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();
            $response['message'] = 'Gagal memperbarui data di database.';
        }
    } else {
        $response['message'] = implode(' ', $errors);
    }
}

$mysqli->close();
echo json_encode($response);
?>
