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

    // --- LOGIC FOR beranda.php ---
    const ctx = document.getElementById('reportChart');
    if (ctx && typeof reportChartData !== 'undefined') {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: reportChartData.labels,
                datasets: [{
                    data: reportChartData.data,
                    backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#6c757d']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // --- LOGIC FOR laporan.php ---
    const reportsTable = document.getElementById('reportsTable');
    if (reportsTable) {
        // ... (Polling and status update logic from previous steps)
        // This includes checkForNewReports, reNumberTableRows, etc.
    }

    // --- LOGIC FOR manage_students.php ---
    const studentsTable = document.getElementById('studentsTable');
    if (studentsTable) {
        // ... (All student CRUD modal logic)
    }

    // --- LOGIC FOR manage_teachers.php ---
    const teachersTable = document.getElementById('teachersTable');
    if (teachersTable) {
        // ... (All teacher CRUD modal logic)
    }
});
