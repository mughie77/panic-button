document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    const statusUpdateMessage = document.getElementById('statusUpdateMessage');

    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const reportId = this.dataset.reportId;
            const newStatus = this.value;

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
                    // Show success message
                    statusUpdateMessage.className = 'alert alert-success';
                    statusUpdateMessage.textContent = data.message;
                    statusUpdateMessage.style.display = 'block';

                    // Update the badge in the table
                    const statusCell = document.querySelector(`#report-row-${reportId} .status-cell`);
                    if (statusCell) {
                        let badgeClass = 'bg-warning text-dark';
                        if (newStatus === 'Dalam Penanganan') badgeClass = 'bg-primary';
                        if (newStatus === 'Selesai') badgeClass = 'bg-success';
                        statusCell.innerHTML = `<span class="badge ${badgeClass}">${newStatus}</span>`;
                    }
                } else {
                    // Show error message
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
        });
    });

    // Add confirmation for all delete buttons
    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('delete-btn')) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat diurungkan.')) {
                event.preventDefault();
            }
        }
    });
});
