<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isApplicant()) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all applications for the current user
$stmt = $pdo->prepare("
    SELECT a.*, j.title as job_title, j.department, j.location,
           j.salary_range, i.scheduled_time as interview_time
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    LEFT JOIN interviews i ON a.id = i.application_id
    WHERE a.user_id = ?
    ORDER BY a.applied_at DESC
");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>My Applications</h2>
    
    <?php if (empty($applications)): ?>
        <div class="alert alert-info mt-4">
            You haven't applied to any jobs yet. <a href="../jobs/index.php">Browse available jobs</a>.
        </div>
    <?php else: ?>
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Salary</th>
                        <th>Applied On</th>
                        <th>Status</th>
                        <th>Interview</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['job_title']) ?></td>
                            <td><?= htmlspecialchars($app['department']) ?></td>
                            <td><?= htmlspecialchars($app['location']) ?></td>
                            <td><?= htmlspecialchars($app['salary_range'] ?? 'Negotiable') ?></td>
                            <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                            <td>
                                <span class="badge bg-<?= 
                                    $app['status'] === 'applied' ? 'secondary' : 
                                    ($app['status'] === 'reviewed' ? 'info' : 
                                    ($app['status'] === 'interview' ? 'warning' : 
                                    ($app['status'] === 'hired' ? 'success' : 'danger')))
                                ?>">
                                    <?= ucfirst($app['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($app['interview_time']): ?>
                                    <?= date('M d, Y H:i', strtotime($app['interview_time'])) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>