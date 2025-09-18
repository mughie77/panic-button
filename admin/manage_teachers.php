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

<div id="crud-message" class="alert" style="display: none;"></div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="h2 mb-0">Manajemen Guru</h1>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
            <i class="bi bi-plus-circle"></i> Tambah Guru
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="teachersTable">
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
                            <tr id="teacher-row-<?php echo $teacher['user_id']; ?>">
                                <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm edit-btn-teacher" data-bs-toggle="modal" data-bs-target="#editTeacherModal" data-id="<?php echo $teacher['user_id']; ?>">Edit</button>
                                    <?php if ($teacher['user_id'] != $_SESSION['user_id']): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-btn-teacher" data-bs-toggle="modal" data-bs-target="#deleteTeacherModal" data-id="<?php echo $teacher['user_id']; ?>">Hapus</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="no-teachers-row">
                            <td colspan="3" class="text-center">Belum ada data guru.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Guru Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addTeacherForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="add_teacher_full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="add_teacher_full_name" name="full_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="add_teacher_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="add_teacher_username" name="username" required>
                </div>
            </div>
            <div class="row">
                 <div class="col-md-6 mb-3">
                    <label for="add_teacher_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="add_teacher_password" name="password" required>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="addTeacherForm">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Teacher Modal -->
<div class="modal fade" id="editTeacherModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Guru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editTeacherForm">
            <input type="hidden" id="edit_teacher_user_id" name="user_id">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="edit_teacher_full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="edit_teacher_full_name" name="full_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_teacher_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="edit_teacher_username" name="username" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="edit_teacher_password" class="form-label">Password Baru (Opsional)</label>
                    <input type="password" class="form-control" id="edit_teacher_password" name="password">
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="editTeacherForm">Simpan Perubahan</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Teacher Modal -->
<div class="modal fade" id="deleteTeacherModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data guru ini? Tindakan ini tidak dapat diurungkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteTeacherBtn">Hapus</button>
      </div>
    </div>
  </div>
</div>

<?php
require_once 'includes/footer.php';
?>
