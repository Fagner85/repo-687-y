<?php

require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header('Location: ../auth/login.php');
    exit();
}

// Get stats
$stmt = $pdo->query("SELECT COUNT(*) as total_jobs FROM jobs");
$total_jobs = $stmt->fetch(PDO::FETCH_ASSOC)['total_jobs'];

$stmt = $pdo->query("SELECT COUNT(*) as total_applicants FROM users WHERE role = 'applicant'");
$total_applicants = $stmt->fetch(PDO::FETCH_ASSOC)['total_applicants'];

$stmt = $pdo->query("SELECT COUNT(*) as total_applications FROM applications");
$total_applications = $stmt->fetch(PDO::FETCH_ASSOC)['total_applications'];

$stmt = $pdo->query("SELECT COUNT(*) as pending_applications FROM applications WHERE status = 'applied'");
$pending_applications = $stmt->fetch(PDO::FETCH_ASSOC)['pending_applications'];

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Jobs</h5>
                    <p class="card-text display-4"><?php echo $total_jobs; ?></p>
                    <a href="jobs.php" class="text-white">View Jobs</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Applicants</h5>
                    <p class="card-text display-4"><?php echo $total_applicants; ?></p>
                    <a href="applicants.php" class="text-white">View Applicants</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Applications</h5>
                    <p class="card-text display-4"><?php echo $total_applications; ?></p>
                    <a href="applications.php" class="text-white">View Applications</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending Applications</h5>
                    <p class="card-text display-4"><?php echo $pending_applications; ?></p>
                    <a href="applications.php?status=applied" class="text-dark">Review Now</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Applications -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Recent Applications</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Job Title</th>
                            <th>Applied At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("
                            SELECT a.id, u.email, j.title, a.applied_at, a.status 
                            FROM applications a
                            JOIN users u ON a.user_id = u.id
                            JOIN jobs j ON a.job_id = j.id
                            ORDER BY a.applied_at DESC
                            LIMIT 5
                        ");
                        
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['title']}</td>
                                <td>" . date('M d, Y H:i', strtotime($row['applied_at'])) . "</td>
                                <td><span class='badge bg-" . getStatusBadge($row['status']) . "'>{$row['status']}</span></td>
                                <td>
                                    <a href='application_view.php?id={$row['id']}' class='btn btn-sm btn-primary'>View</a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
function getStatusBadge($status) {
    switch ($status) {
        case 'applied': return 'secondary';
        case 'reviewed': return 'info';
        case 'interview': return 'warning';
        case 'hired': return 'success';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

include '../includes/footer.php';
?>