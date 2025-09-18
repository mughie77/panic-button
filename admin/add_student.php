<?php
require_once 'includes/header.php';

$errors = [];
$full_name = '';
$class = '';
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $full_name = sanitize_input($_POST['full_name']);
    $class = sanitize_input($_POST['class']);
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    if (empty($full_name)) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($class)) $errors[] = 'Kelas wajib diisi.';
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
        $role = 'siswa';

        // Use a transaction to ensure both inserts succeed or fail together
        $mysqli->begin_transaction();

        try {
            // Insert into users table
            $stmt_user = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt_user->bind_param("sss", $username, $hashed_password, $role);
            $stmt_user->execute();

            // Get the new user's ID
            $user_id = $mysqli->insert_id;

            // Insert into students table
            $stmt_student = $mysqli->prepare("INSERT INTO students (user_id, full_name, class) VALUES (?, ?, ?)");
            $stmt_student->bind_param("iss", $user_id, $full_name, $class);
            $stmt_student->execute();

            // If we get here, both queries were successful, so commit the transaction
            $mysqli->commit();

            redirect('manage_students.php?success=add');

        } catch (mysqli_sql_exception $exception) {
            // If anything goes wrong, roll back the transaction
            $mysqli->rollback();
            $errors[] = 'Gagal membuat akun siswa. Terjadi kesalahan database.';
        }

        $stmt_user->close();
        $stmt_student->close();
    }
    $mysqli->close();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tambah Siswa Baru</h1>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="add_student.php" method="POST">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="class" class="form-label">Kelas</label>
                <input type="text" class="form-control" id="class" name="class" value="<?php echo htmlspecialchars($class); ?>" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text">Password akan di-hash untuk keamanan.</div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="manage_students.php" class="btn btn-secondary">Batal</a>
</form>

<?php
require_once 'includes/footer.php';
?>
