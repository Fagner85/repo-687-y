<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    redirect('../auth/login.php');
}

// Handle job deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $job_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->execute([$job_id]);
    $_SESSION['success'] = "Job deleted successfully";
    redirect('jobs.php');
}

// Get all jobs
$stmt = $pdo->query("
    SELECT j.*, u.email as posted_by_email 
    FROM jobs j
    JOIN users u ON j.posted_by = u.id
    ORDER BY j.posted_at DESC
");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Jobs</h2>
        <a href="job_add.php" class="btn btn-primary">Add New Job</a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Location</th>
                            <th>Posted By</th>
                            <th>Posted At</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?php echo $job['id']; ?></td>
                                <td><?php echo $job['title']; ?></td>
                                <td><?php echo $job['department'] ?? '-'; ?></td>
                                <td><?php echo $job['location']; ?></td>
                                <td><?php echo $job['posted_by_email']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($job['posted_at'])); ?></td>
                                <td><?php echo $job['deadline'] ? date('M d, Y', strtotime($job['deadline'])) : '-'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $job['is_active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $job['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="job_edit.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="job_view.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="jobs.php?delete=<?php echo $job['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>