<?php
require_once 'includes/header.php';

// Fetch all users from the users table
$query = "
    SELECT
        id,
        username,
        role,
        created_at
    FROM users
    ORDER BY created_at DESC
";

$result = $mysqli->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);

$mysqli->close();
?>

<div class="card">
    <div class="card-header">
        <h1 class="h2">Manajemen User</h1>
    </div>
    <div class="card-body">
        <p class="text-muted">Halaman ini menampilkan semua pengguna yang terdaftar di sistem. Untuk mengelola pengguna, silakan gunakan halaman Manajemen Siswa atau Manajemen Guru.</p>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID User</th>
                        <th>Username</th>
                        <th>Peran (Role)</th>
                        <th>Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo ucfirst($user['role']); ?></td>
                                <td><?php echo $user['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada pengguna yang terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
