<?php
require_once 'includes/header.php';

// Fetch all students with their user details
$query = "
    SELECT
        s.id as student_id,
        s.full_name,
        s.class,
        u.id as user_id,
        u.username
    FROM students s
    JOIN users u ON s.user_id = u.id
    ORDER BY s.full_name ASC
";

$result = $mysqli->query($query);
$students = $result->fetch_all(MYSQLI_ASSOC);

$mysqli->close();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen Siswa</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_student.php" class="btn btn-sm btn-outline-secondary">
            Tambah Siswa Baru
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php
            if ($_GET['success'] == 'add') echo 'Siswa baru berhasil ditambahkan.';
            if ($_GET['success'] == 'update') echo 'Data siswa berhasil diperbarui.';
            if ($_GET['success'] == 'delete') echo 'Data siswa berhasil dihapus.';
        ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                        <td>
                            <a href="edit_student.php?id=<?php echo $student['user_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_student.php?id=<?php echo $student['user_id']; ?>" class="btn btn-danger btn-sm delete-btn">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Belum ada data siswa.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
?>
