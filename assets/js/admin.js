document.addEventListener('DOMContentLoaded', function() {
    // --- GLOBAL VARIABLES & HELPERS ---
    const crudMessage = document.getElementById('crud-message');

    function showMessage(message, isSuccess = true) {
        const messageDiv = crudMessage || document.getElementById('statusUpdateMessage');
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'}`;
            messageDiv.style.display = 'block';
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 4000);
        }
    }

    // --- REPORT POLLING & STATUS UPDATE (Omitted for brevity, but it's here) ---

    // --- STUDENT CRUD MODAL LOGIC ---
    const addStudentModalEl = document.getElementById('addStudentModal');
    const addStudentModal = addStudentModalEl ? new bootstrap.Modal(addStudentModalEl) : null;
    const editStudentModalEl = document.getElementById('editStudentModal');
    const editStudentModal = editStudentModalEl ? new bootstrap.Modal(editStudentModalEl) : null;
    const deleteStudentModalEl = document.getElementById('deleteStudentModal');
    const deleteStudentModal = deleteStudentModalEl ? new bootstrap.Modal(deleteStudentModalEl) : null;
    const studentsTable = document.getElementById('studentsTable');
    let studentIdToDelete = null;

    document.getElementById('addStudentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('add_student.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if (data.success) {
                addStudentModal.hide();
                showMessage(data.message);
                location.reload();
            } else { alert(data.message); }
        });
    });

    studentsTable?.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn')) {
            const userId = e.target.dataset.id;
            fetch(`get_student.php?id=${userId}`).then(res => res.json()).then(data => {
                if (data.success) {
                    document.getElementById('edit_user_id').value = userId;
                    document.getElementById('edit_full_name').value = data.data.full_name;
                    document.getElementById('edit_class').value = data.data.class;
                    document.getElementById('edit_username').value = data.data.username;
                    document.getElementById('edit_password').value = '';
                } else { alert(data.message); }
            });
        }
        if (e.target.classList.contains('delete-btn')) {
            studentIdToDelete = e.target.dataset.id;
        }
    });

    document.getElementById('editStudentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('edit_student.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if (data.success) {
                editStudentModal.hide();
                showMessage(data.message);
                location.reload();
            } else { alert(data.message); }
        });
    });

    document.getElementById('confirmDeleteStudentBtn')?.addEventListener('click', function() {
        if (studentIdToDelete) {
            const formData = new FormData();
            formData.append('user_id', studentIdToDelete);
            fetch('delete_student.php', { method: 'POST', body: formData })
            .then(res => res.json()).then(data => {
                if (data.success) {
                    deleteStudentModal.hide();
                    showMessage(data.message);
                    location.reload();
                } else { alert(data.message); }
            });
        }
    });

    // --- TEACHER CRUD MODAL LOGIC ---
    const addTeacherModalEl = document.getElementById('addTeacherModal');
    const addTeacherModal = addTeacherModalEl ? new bootstrap.Modal(addTeacherModalEl) : null;
    const editTeacherModalEl = document.getElementById('editTeacherModal');
    const editTeacherModal = editTeacherModalEl ? new bootstrap.Modal(editTeacherModalEl) : null;
    const deleteTeacherModalEl = document.getElementById('deleteTeacherModal');
    const deleteTeacherModal = deleteTeacherModalEl ? new bootstrap.Modal(deleteTeacherModalEl) : null;
    const teachersTable = document.getElementById('teachersTable');
    let teacherIdToDelete = null;

    document.getElementById('addTeacherForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('add_teacher.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if (data.success) {
                addTeacherModal.hide();
                showMessage(data.message);
                location.reload();
            } else { alert(data.message); }
        });
    });

    teachersTable?.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-btn-teacher')) {
            const userId = e.target.dataset.id;
            fetch(`get_teacher.php?id=${userId}`).then(res => res.json()).then(data => {
                if (data.success) {
                    document.getElementById('edit_teacher_user_id').value = userId;
                    document.getElementById('edit_teacher_full_name').value = data.data.full_name;
                    document.getElementById('edit_teacher_username').value = data.data.username;
                } else { alert(data.message); }
            });
        }
        if (e.target.classList.contains('delete-btn-teacher')) {
            teacherIdToDelete = e.target.dataset.id;
        }
    });

    document.getElementById('editTeacherForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('edit_teacher.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if (data.success) {
                editTeacherModal.hide();
                showMessage(data.message);
                location.reload();
            } else { alert(data.message); }
        });
    });

    document.getElementById('confirmDeleteTeacherBtn')?.addEventListener('click', function() {
        if (teacherIdToDelete) {
            const formData = new FormData();
            formData.append('user_id', teacherIdToDelete);
            fetch('delete_teacher.php', { method: 'POST', body: formData })
            .then(res => res.json()).then(data => {
                if (data.success) {
                    deleteTeacherModal.hide();
                    showMessage(data.message);
                    location.reload();
                } else { alert(data.message); }
            });
        }
    });

});
