<?php
require_once 'includes/header.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id <= 0) {
    redirect('manage_students.php');
}

$errors = [];

// Fetch current student data
$stmt = $mysqli->prepare("
    SELECT s.full_name, s.class, u.username
    FROM students s
    JOIN users u ON s.user_id = u.id
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    redirect('manage_students.php');
}

$full_name = $student['full_name'];
$class = $student['class'];
$username = $student['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $full_name = sanitize_input($_POST['full_name']);
    $class = sanitize_input($_POST['class']);
    $username = sanitize_input($_POST['username']);
    $password = sanitize_input($_POST['password']);

    if (empty($full_name)) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($class)) $errors[] = 'Kelas wajib diisi.';
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
                // If password is provided, update it
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt_user = $mysqli->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
                $stmt_user->bind_param("ssi", $username, $hashed_password, $user_id);
            } else {
                // If no password, update username only
                $stmt_user = $mysqli->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmt_user->bind_param("si", $username, $user_id);
            }
            $stmt_user->execute();

            // Update students table
            $stmt_student = $mysqli->prepare("UPDATE students SET full_name = ?, class = ? WHERE user_id = ?");
            $stmt_student->bind_param("ssi", $full_name, $class, $user_id);
            $stmt_student->execute();

            $mysqli->commit();
            redirect('manage_students.php?success=update');

        } catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();
            $errors[] = 'Gagal memperbarui data siswa. Terjadi kesalahan database.';
        }
        $stmt_user->close();
        $stmt_student->close();
    }
    $mysqli->close();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Data Siswa</h1>
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

<form action="edit_student.php?id=<?php echo $user_id; ?>" method="POST">
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
                <label for="password" class="form-label">Password Baru (Opsional)</label>
                <input type="password" class="form-control" id="password" name="password">
                <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    <a href="manage_students.php" class="btn btn-secondary">Batal</a>
</form>

<?php
require_once 'includes/footer.php';
?>
