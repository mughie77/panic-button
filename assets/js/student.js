document.addEventListener('DOMContentLoaded', function() {
    const panicButton = document.getElementById('panicButton');
    const statusMessage = document.getElementById('statusMessage');
    const reportsTableBody = document.getElementById('reportsTableBody');

    // Helper function to escape HTML to prevent XSS
    function escapeHTML(str) {
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    }

    if (panicButton) {
        panicButton.addEventListener('click', function() {
            // Disable button to prevent multiple clicks
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';

            fetch('submit_report.php', {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    statusMessage.className = 'alert alert-success';
                    statusMessage.textContent = data.message;
                    statusMessage.style.display = 'block';

                    // Add new report to the top of the table after escaping data
                    const newRow = reportsTableBody.insertRow(0);
                    const escapedId = escapeHTML(data.report.id);
                    const escapedTime = escapeHTML(data.report.report_time);
                    const statusBadge = data.report.status === 'Belum Diproses' ? '<span class="badge bg-warning text-dark">Belum Diproses</span>' : '';

                    newRow.innerHTML = `
                        <td>${escapedId}</td>
                        <td>${escapedTime}</td>
                        <td>${statusBadge}</td>
                    `;
                } else {
                    // Show error message
                    statusMessage.className = 'alert alert-danger';
                    statusMessage.textContent = data.message || 'Terjadi kesalahan.';
                    statusMessage.style.display = 'block';
                }

                // Re-enable button
                this.disabled = false;
                this.innerHTML = 'TEKAN TOMBOL PANIK';
            })
            .catch(error => {
                // Show network or other error
                statusMessage.className = 'alert alert-danger';
                statusMessage.textContent = 'Gagal terhubung ke server.';
                statusMessage.style.display = 'block';

                // Re-enable button
                this.disabled = false;
                this.innerHTML = 'TEKAN TOMBOL PANIK';
            });
        });
    }
});
