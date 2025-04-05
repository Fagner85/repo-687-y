
<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header('Location: ../auth/login.php');
    exit();
}

// Get report data
$jobs_by_department = $pdo->query("
    SELECT department, COUNT(*) as count 
    FROM jobs 
    WHERE is_active = TRUE
    GROUP BY department
    ORDER BY count DESC
")->fetchAll(PDO::FETCH_ASSOC);

$applications_by_status = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM applications 
    GROUP BY status
    ORDER BY count DESC
")->fetchAll(PDO::FETCH_ASSOC);

$monthly_applications = $pdo->query("
    SELECT DATE_FORMAT(applied_at, '%Y-%m') as month, 
           COUNT(*) as count
    FROM applications
    GROUP BY month
    ORDER BY month DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>System Reports</h2>
    
    <div class="row mt-4">
        <!-- Jobs by Department -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Jobs by Department</h5>
                </div>
                <div class="card-body">
                    <canvas id="jobsByDepartmentChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Applications by Status -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Applications by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="applicationsByStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Applications -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Monthly Applications (Last 6 Months)</h5>
        </div>
        <div class="card-body">
            <canvas id="monthlyApplicationsChart" height="150"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Jobs by Department Chart
new Chart(document.getElementById('jobsByDepartmentChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($jobs_by_department, 'department')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($jobs_by_department, 'count')) ?>,
            backgroundColor: [
                '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545', '#fd7e14'
            ]
        }]
    }
});

// Applications by Status Chart
new Chart(document.getElementById('applicationsByStatusChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($applications_by_status, 'status')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($applications_by_status, 'count')) ?>,
            backgroundColor: [
                '#6c757d', // applied - gray
                '#0dcaf0', // reviewed - teal
                '#ffc107', // interview - yellow
                '#198754', // hired - green
                '#dc3545'  // rejected - red
            ]
        }]
    }
});

// Monthly Applications Chart
new Chart(document.getElementById('monthlyApplicationsChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($monthly_applications, 'month')) ?>,
        datasets: [{
            label: 'Applications',
            data: <?= json_encode(array_column($monthly_applications, 'count')) ?>,
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>