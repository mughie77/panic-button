document.addEventListener('DOMContentLoaded', function() {
    // --- STATUS UPDATE LOGIC ---
    const statusUpdateMessage = document.getElementById('statusUpdateMessage');
    const reportsTable = document.getElementById('reportsTable');

    // Use event delegation for status updates since rows will be added dynamically
    if (reportsTable) {
        reportsTable.addEventListener('change', function(event) {
            if (event.target.classList.contains('status-select')) {
                const select = event.target;
                const reportId = select.dataset.reportId;
                const newStatus = select.value;

                const formData = new FormData();
                formData.append('report_id', reportId);
                formData.append('status', newStatus);

                fetch('update_report_status.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusUpdateMessage.className = 'alert alert-success';
                        statusUpdateMessage.textContent = data.message;
                        statusUpdateMessage.style.display = 'block';

                        const statusCell = document.querySelector(`#report-row-${reportId} .status-cell`);
                        if (statusCell) {
                            let badgeClass = 'bg-warning text-dark';
                            if (newStatus === 'Dalam Penanganan') badgeClass = 'bg-primary';
                            if (newStatus === 'Selesai') badgeClass = 'bg-success';
                            statusCell.innerHTML = `<span class="badge ${badgeClass}">${newStatus}</span>`;
                        }
                    } else {
                        statusUpdateMessage.className = 'alert alert-danger';
                        statusUpdateMessage.textContent = data.message || 'Gagal memperbarui status.';
                        statusUpdateMessage.style.display = 'block';
                    }
                })
                .catch(error => {
                    statusUpdateMessage.className = 'alert alert-danger';
                    statusUpdateMessage.textContent = 'Gagal terhubung ke server.';
                    statusUpdateMessage.style.display = 'block';
                });
            }
        });
    }

    // --- NEW REPORT POLLING LOGIC ---
    let lastReportId = 0;
    const notificationSound = document.getElementById('notificationSound');

    // Get the initial latest ID from the first row of the table
    const firstRow = reportsTable ? reportsTable.querySelector('tbody tr') : null;
    if (firstRow && firstRow.id) {
        lastReportId = parseInt(firstRow.id.replace('report-row-', ''), 10) || 0;
    }

    function getStatusBadge(status) {
        switch (status) {
            case 'Dalam Penanganan': return '<span class="badge bg-primary">Dalam Penanganan</span>';
            case 'Selesai': return '<span class="badge bg-success">Selesai</span>';
            default: return '<span class="badge bg-warning text-dark">Belum Diproses</span>';
        }
    }

    function prependReportRow(report) {
        const tableBody = reportsTable.querySelector('tbody');
        const newRow = tableBody.insertRow(0);
        newRow.id = `report-row-${report.id}`;
        newRow.innerHTML = `
            <td>${report.id}</td>
            <td>${report.report_time}</td>
            <td>${report.full_name}</td>
            <td>${report.username}</td>
            <td class="status-cell">${getStatusBadge(report.status)}</td>
            <td>
                <select class="form-select form-select-sm status-select" data-report-id="${report.id}">
                    <option value="Belum Diproses" ${report.status === 'Belum Diproses' ? 'selected' : ''}>Belum Diproses</option>
                    <option value="Dalam Penanganan" ${report.status === 'Dalam Penanganan' ? 'selected' : ''}>Dalam Penanganan</option>
                    <option value="Selesai" ${report.status === 'Selesai' ? 'selected' : ''}>Selesai</option>
                </select>
            </td>
        `;
    }

    function checkForNewReports() {
        fetch(`check_new_reports.php?last_id=${lastReportId}`)
            .then(response => response.json())
            .then(data => {
                if (data.reports && data.reports.length > 0) {
                    // Play sound
                    if(notificationSound) {
                        notificationSound.play().catch(e => console.error("Audio play failed: ", e));
                    }

                    // Add new reports to the table
                    data.reports.forEach(report => {
                        prependReportRow(report);
                        // Update the last known ID to the latest one
                        lastReportId = Math.max(lastReportId, report.id);
                    });
                }
            })
            .catch(error => console.error('Polling error:', error));
    }

    // Start polling every 10 seconds
    setInterval(checkForNewReports, 10000);


    // --- DELETE CONFIRMATION ---
    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat diurungkan.')) {
                event.preventDefault();
            }
        }
    });
});
