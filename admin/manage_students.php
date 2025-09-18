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

<div id="crud-message" class="alert" style="display: none;"></div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1 class="h2 mb-0">Manajemen Siswa</h1>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="bi bi-plus-circle"></i> Tambah Siswa
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="studentsTable">
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
                            <tr id="student-row-<?php echo $student['user_id']; ?>">
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editStudentModal" data-id="<?php echo $student['user_id']; ?>">Edit</button>
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-bs-toggle="modal" data-bs-target="#deleteStudentModal" data-id="<?php echo $student['user_id']; ?>">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="no-students-row">
                            <td colspan="4" class="text-center">Belum ada data siswa.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Siswa Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="addStudentForm">
            <!-- Form fields will be similar to the old add_student.php page -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="add_full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="add_full_name" name="full_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="add_class" class="form-label">Kelas</label>
                    <input type="text" class="form-control" id="add_class" name="class" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="add_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="add_username" name="username" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="add_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="add_password" name="password" required>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="addStudentForm">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editStudentForm">
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="edit_full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_class" class="form-label">Kelas</label>
                    <input type="text" class="form-control" id="edit_class" name="class" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="edit_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="edit_username" name="username" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_password" class="form-label">Password Baru (Opsional)</label>
                    <input type="password" class="form-control" id="edit_password" name="password">
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="editStudentForm">Simpan Perubahan</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Student Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data siswa ini? Tindakan ini tidak dapat diurungkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteStudentBtn">Hapus</button>
      </div>
    </div>
  </div>
</div>


<?php
require_once 'includes/footer.php';
?>
