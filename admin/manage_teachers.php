<?php
require_once 'includes/header.php';

// Fetch all teachers with their user details
$query = "
    SELECT
        t.id as teacher_id,
        t.full_name,
        u.id as user_id,
        u.username
    FROM teachers t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.full_name ASC
";

$result = $mysqli->query($query);
$teachers = $result->fetch_all(MYSQLI_ASSOC);

$mysqli->close();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen Guru</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="add_teacher.php" class="btn btn-sm btn-outline-secondary">
            Tambah Guru Baru
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php
            if ($_GET['success'] == 'add') echo 'Guru baru berhasil ditambahkan.';
            if ($_GET['success'] == 'update') echo 'Data guru berhasil diperbarui.';
            if ($_GET['success'] == 'delete') echo 'Data guru berhasil dihapus.';
        ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($teachers) > 0): ?>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                        <td>
                            <a href="edit_teacher.php?id=<?php echo $teacher['user_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <!-- Prevent admin from deleting themselves -->
                            <?php if ($teacher['user_id'] != $_SESSION['user_id']): ?>
                                <a href="delete_teacher.php?id=<?php echo $teacher['user_id']; ?>" class="btn btn-danger btn-sm delete-btn">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Belum ada data guru.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
?>
