document.addEventListener('DOMContentLoaded', function() {
    // --- GLOBAL HELPERS ---
    function showMessage(message, isSuccess = true) {
        const messageDiv = document.getElementById('crud-message') || document.getElementById('statusUpdateMessage');
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'}`;
            messageDiv.style.display = 'block';
            setTimeout(() => { messageDiv.style.display = 'none'; }, 4000);
        }
    }

    // --- NOTIFICATION LOGIC ---
    const notificationBadge = document.getElementById('notificationBadge');
    const laporanLink = document.getElementById('laporanLink');
    const originalTitle = document.title;
    let newReportCount = 0;

    function showNotification(count) {
        if (notificationBadge) notificationBadge.style.display = 'block';
        newReportCount += count;
        document.title = `(${newReportCount}) Laporan Baru!`;
    }

    function clearNotification() {
        if (notificationBadge) notificationBadge.style.display = 'none';
        newReportCount = 0;
        document.title = originalTitle;
    }

    if (laporanLink) {
        laporanLink.addEventListener('click', clearNotification);
    }
    if (window.location.pathname.includes('laporan.php')) {
        clearNotification();
    }

    // --- LOGIC FOR beranda.php (Chart) ---
    const ctx = document.getElementById('reportChart');
    if (ctx && typeof reportChartData !== 'undefined') {
        new Chart(ctx, { type: 'pie', data: { labels: reportChartData.labels, datasets: [{ data: reportChartData.data, backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#6c757d'] }] }, options: { responsive: true, maintainAspectRatio: false } });
    }

    // --- LOGIC FOR laporan.php (Polling & Status Updates) ---
    const reportsTable = document.getElementById('reportsTable');
    if (reportsTable) {
        reportsTable.addEventListener('change', function(event) {
            if (event.target.classList.contains('status-select')) {
                const select = event.target;
                const formData = new FormData();
                formData.append('report_id', select.dataset.reportId);
                formData.append('status', select.value);
                fetch('update_report_status.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => {
                    if (data.success) {
                        showMessage(data.message, true);
                        document.querySelector(`#report-row-${select.dataset.reportId} .status-cell`).innerHTML = getStatusBadge(select.value);
                    } else { showMessage(data.message, false); }
                });
            }
        });

        let lastReportId = 0;
        const notificationSound = document.getElementById('notificationSound');
        const firstRow = reportsTable.querySelector('tbody tr');
        if (firstRow && firstRow.id) {
            lastReportId = parseInt(firstRow.id.replace('report-row-', ''), 10) || 0;
        }

        function getStatusBadge(status) {
            if (status === 'Dalam Penanganan') return '<span class="badge bg-primary">Dalam Penanganan</span>';
            if (status === 'Selesai') return '<span class="badge bg-success">Selesai</span>';
            if (status === 'Laporan Palsu') return '<span class="badge bg-secondary">Laporan Palsu</span>';
            return '<span class="badge bg-warning text-dark">Belum Diproses</span>';
        }

        function reNumberTableRows() {
            const rows = reportsTable.querySelectorAll('tbody tr');
            rows.forEach((row, index) => { row.querySelector('td:first-child').textContent = index + 1; });
        }

        function prependReportRow(report) {
            const tableBody = reportsTable.querySelector('tbody');
            const newRow = tableBody.insertRow(0);
            newRow.id = `report-row-${report.id}`;
            newRow.innerHTML = `<td>*</td><td>${report.report_time}</td><td>${report.full_name}</td><td>${report.class}</td><td class="status-cell">${getStatusBadge('Belum Diproses')}</td><td><select class="form-select form-select-sm status-select" data-report-id="${report.id}"><option value="Belum Diproses" selected>Belum Diproses</option><option value="Dalam Penanganan">Dalam Penanganan</option><option value="Selesai">Selesai</option><option value="Laporan Palsu">Laporan Palsu</option></select></td>`;
        }

        function checkForNewReports() {
            fetch(`check_new_reports.php?last_id=${lastReportId}`).then(response => response.json()).then(data => {
                if (data.reports && data.reports.length > 0) {
                    if (notificationSound) notificationSound.play().catch(e => console.error("Audio play failed: ", e));
                    showNotification(data.reports.length);
                    data.reports.forEach(report => {
                        prependReportRow(report);
                        lastReportId = Math.max(lastReportId, report.id);
                    });
                    reNumberTableRows();
                }
            }).catch(error => console.error('Polling error:', error));
        }
        setInterval(checkForNewReports, 5000);
    }

    // --- LOGIC FOR manage_students.php ---
    const studentsTable = document.getElementById('studentsTable');
    if (studentsTable) {
        const addStudentModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
        const editStudentModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
        const deleteStudentModal = new bootstrap.Modal(document.getElementById('deleteStudentModal'));
        let studentIdToDelete = null;

        document.getElementById('addStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('add_student.php', { method: 'POST', body: new FormData(this) }).then(res => res.json()).then(data => {
                if (data.success) { addStudentModal.hide(); showMessage(data.message, true, true); setTimeout(() => location.reload(), 1500); } else { alert(data.message); }
            });
        });
        studentsTable.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn')) {
                const userId = e.target.dataset.id;
                fetch(`get_student.php?id=${userId}`).then(res => res.json()).then(data => {
                    if (data.success) {
                        document.getElementById('edit_user_id').value = userId;
                        document.getElementById('edit_full_name').value = data.data.full_name;
                        document.getElementById('edit_class').value = data.data.class;
                        document.getElementById('edit_username').value = data.data.username;
                    } else { alert(data.message); }
                });
            }
            if (e.target.classList.contains('delete-btn')) { studentIdToDelete = e.target.dataset.id; }
        });
        document.getElementById('editStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('edit_student.php', { method: 'POST', body: new FormData(this) }).then(res => res.json()).then(data => {
                if (data.success) { editStudentModal.hide(); showMessage(data.message, true, true); setTimeout(() => location.reload(), 1500); } else { alert(data.message); }
            });
        });
        document.getElementById('confirmDeleteStudentBtn').addEventListener('click', function() {
            if (studentIdToDelete) {
                const formData = new FormData();
                formData.append('user_id', studentIdToDelete);
                fetch('delete_student.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => {
                    if (data.success) { deleteStudentModal.hide(); showMessage(data.message, true, true); setTimeout(() => location.reload(), 1500); } else { alert(data.message); }
                });
            }
        });
    }

    // --- LOGIC FOR manage_teachers.php ---
    const teachersTable = document.getElementById('teachersTable');
    if (teachersTable) {
        const addTeacherModal = new bootstrap.Modal(document.getElementById('addTeacherModal'));
        const editTeacherModal = new bootstrap.Modal(document.getElementById('editTeacherModal'));
        const deleteTeacherModal = new bootstrap.Modal(document.getElementById('deleteTeacherModal'));
        let teacherIdToDelete = null;

        document.getElementById('addTeacherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('add_teacher.php', { method: 'POST', body: new FormData(this) }).then(res => res.json()).then(data => {
                if (data.success) { addTeacherModal.hide(); showMessage(data.message, true, true); setTimeout(() => location.reload(), 1500); } else { alert(data.message); }
            });
        });
        teachersTable.addEventListener('click', function(e) {
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
            if (e.target.classList.contains('delete-btn-teacher')) { teacherIdToDelete = e.target.dataset.id; }
        });
        document.getElementById('editTeacherForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('edit_teacher.php', { method: 'POST', body: new FormData(this) }).then(res => res.json()).then(data => {
                if (data.success) { editTeacherModal.hide(); showMessage(data.message, true, true); setTimeout(() => location.reload(), 1500); } else { alert(data.message); }
            });
        });
        document.getElementById('confirmDeleteTeacherBtn').addEventListener('click', function() {
            if (teacherIdToDelete) {
                const formData = new FormData();
                formData.append('user_id', teacherIdToDelete);
                fetch('delete_teacher.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => {
                    if (data.success) { deleteTeacherModal.hide(); showMessage(data.message, true, true); setTimeout(() => location.reload(), 1500); } else { alert(data.message); }
                });
            }
        });
    }
});
