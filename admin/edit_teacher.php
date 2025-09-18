<?php
require_once 'includes/header.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id <= 0) {
    redirect('manage_teachers.php');
}

$errors = [];

// Fetch current teacher data
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

if (!$teacher) {
    redirect('manage_teachers.php');
}

$full_name = $teacher['full_name'];
$username = $teacher['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    if (empty($full_name)) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($username)) $errors[] = 'Username wajib diisi.';

    // Check if username already exists (and doesn't belong to the current user)
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = 'Username sudah digunakan. Silakan pilih yang lain.';
    }
    $stmt->close();

    if (empty($errors)) {
        $mysqli->begin_transaction();
        try {
            // Update users table
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt_user = $mysqli->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
                $stmt_user->bind_param("ssi", $username, $hashed_password, $user_id);
            } else {
                $stmt_user = $mysqli->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmt_user->bind_param("si", $username, $user_id);
            }
            $stmt_user->execute();

            // Update teachers table
            $stmt_teacher = $mysqli->prepare("UPDATE teachers SET full_name = ? WHERE user_id = ?");
            $stmt_teacher->bind_param("si", $full_name, $user_id);
            $stmt_teacher->execute();

            $mysqli->commit();
            redirect('manage_teachers.php?success=update');

        } catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();
            $errors[] = 'Gagal memperbarui data guru. Terjadi kesalahan database.';
        }
        $stmt_user->close();
        $stmt_teacher->close();
    }
    $mysqli->close();
}
?>

<div class="card">
    <div class="card-header">
        <h1 class="h2">Edit Data Guru</h1>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="edit_teacher.php?id=<?php echo $user_id; ?>" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (Opsional)</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="manage_teachers.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
