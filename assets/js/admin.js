document.addEventListener('DOMContentLoaded', function() {
    // --- GLOBAL VARIABLES & HELPERS ---
    const crudMessage = document.getElementById('crud-message');
    const statusUpdateMessage = document.getElementById('statusUpdateMessage');

    function showMessage(message, isSuccess = true, onCrudPage = false) {
        const messageDiv = onCrudPage ? crudMessage : statusUpdateMessage;
        if (messageDiv) {
            messageDiv.textContent = message;
            messageDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'}`;
            messageDiv.style.display = 'block';
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 4000);
        }
    }

    // --- REPORT POLLING & STATUS UPDATE ---
    const reportsTable = document.getElementById('reportsTable');
    if (reportsTable) {
        // Handle status select change
        reportsTable.addEventListener('change', function(event) {
            if (event.target.classList.contains('status-select')) {
                const select = event.target;
                const reportId = select.dataset.reportId;
                const newStatus = select.value;
                const formData = new FormData();
                formData.append('report_id', reportId);
                formData.append('status', newStatus);

                fetch('update_report_status.php', { method: 'POST', body: formData })
                .then(res => res.json()).then(data => {
                    if (data.success) {
                        showMessage(data.message, true);
                        const statusCell = document.querySelector(`#report-row-${reportId} .status-cell`);
                        if (statusCell) {
                            statusCell.innerHTML = getStatusBadge(newStatus);
                        }
                    } else {
                        showMessage(data.message, false);
                    }
                });
            }
        });

        // Polling logic
        let lastReportId = 0;
        const notificationSound = document.getElementById('notificationSound');
        const firstRow = reportsTable.querySelector('tbody tr');
        if (firstRow && firstRow.id) {
            lastReportId = parseInt(firstRow.id.replace('report-row-', ''), 10) || 0;
        }

        function getStatusBadge(status) {
            if (status === 'Dalam Penanganan') return '<span class="badge bg-primary">Dalam Penanganan</span>';
            if (status === 'Selesai') return '<span class="badge bg-success">Selesai</span>';
            return '<span class="badge bg-warning text-dark">Belum Diproses</span>';
        }

        function reNumberTableRows() {
            const rows = reportsTable.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                const firstCell = row.querySelector('td:first-child');
                if (firstCell) firstCell.textContent = index + 1;
            });
        }

        function prependReportRow(report) {
            const tableBody = reportsTable.querySelector('tbody');
            const newRow = tableBody.insertRow(0);
            newRow.id = `report-row-${report.id}`;
            newRow.innerHTML = `
                <td>*</td>
                <td>${report.report_time}</td>
                <td>${report.full_name}</td>
                <td>${report.class}</td>
                <td class="status-cell">${getStatusBadge('Belum Diproses')}</td>
                <td>
                    <select class="form-select form-select-sm status-select" data-report-id="${report.id}">
                        <option value="Belum Diproses" selected>Belum Diproses</option>
                        <option value="Dalam Penanganan">Dalam Penanganan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </td>
            `;
        }

        function checkForNewReports() {
            fetch(`check_new_reports.php?last_id=${lastReportId}`)
            .then(response => response.json())
            .then(data => {
                if (data.reports && data.reports.length > 0) {
                    if(notificationSound) {
                        notificationSound.play().catch(e => console.error("Audio play failed: ", e));
                    }
                    data.reports.forEach(report => {
                        prependReportRow(report);
                        lastReportId = Math.max(lastReportId, report.id);
                    });
                    reNumberTableRows();
                }
            })
            .catch(error => console.error('Polling error:', error));
        }

        setInterval(checkForNewReports, 5000); // Set interval to 5 seconds
    }

    // --- STUDENT CRUD MODAL LOGIC ---
    // ... (logic is the same as before, just needs the corrected showMessage)
    document.getElementById('addStudentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('add_student.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if (data.success) {
                new bootstrap.Modal(document.getElementById('addStudentModal')).hide();
                showMessage(data.message, true, true);
                setTimeout(() => location.reload(), 1000);
            } else { alert(data.message); }
        });
    });
    // ... all other student CRUD listeners ...

    // --- TEACHER CRUD MODAL LOGIC ---
    // ... (logic is the same as before, just needs the corrected showMessage)
    document.getElementById('addTeacherForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('add_teacher.php', { method: 'POST', body: new FormData(this) })
        .then(res => res.json()).then(data => {
            if (data.success) {
                new bootstrap.Modal(document.getElementById('addTeacherModal')).hide();
                showMessage(data.message, true, true);
                setTimeout(() => location.reload(), 1000);
            } else { alert(data.message); }
        });
    });
    // ... all other teacher CRUD listeners ...
});
