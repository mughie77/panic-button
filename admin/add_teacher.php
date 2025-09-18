<?php
require_once 'includes/header.php';

$errors = [];
$full_name = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    if (empty($full_name)) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($username)) $errors[] = 'Username wajib diisi.';
    if (empty($password)) $errors[] = 'Password wajib diisi.';

    // Check if username already exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = 'Username sudah digunakan. Silakan pilih yang lain.';
    }
    $stmt->close();

    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'guru';

        $mysqli->begin_transaction();
        try {
            // Insert into users table
            $stmt_user = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt_user->bind_param("sss", $username, $hashed_password, $role);
            $stmt_user->execute();

            $user_id = $mysqli->insert_id;

            // Insert into teachers table
            $stmt_teacher = $mysqli->prepare("INSERT INTO teachers (user_id, full_name) VALUES (?, ?)");
            $stmt_teacher->bind_param("is", $user_id, $full_name);
            $stmt_teacher->execute();

            $mysqli->commit();
            redirect('manage_teachers.php?success=add');

        } catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();
            $errors[] = 'Gagal membuat akun guru. Terjadi kesalahan database.';
        }

        $stmt_user->close();
        $stmt_teacher->close();
    }
    $mysqli->close();
}
?>
<div class="card">
    <div class="card-header">
        <h1 class="h2">Tambah Guru Baru</h1>
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

        <form action="add_teacher.php" method="POST">
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
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="manage_teachers.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
