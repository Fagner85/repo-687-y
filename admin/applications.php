<?php
require_once __DIR__ . '/../includes/config.php';

// Ensure the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/auth/login.php');
}

// Fetch applications from the database
$status = $_GET['status'] ?? 'all';

$query = "SELECT applications.id, jobs.title, users.email, applications.status, applications.applied_at
          FROM applications
          JOIN jobs ON applications.job_id = jobs.id
          JOIN users ON applications.user_id = users.id";

if ($status !== 'all') {
    $query .= " WHERE applications.status = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$status]);
} else {
    $stmt = $pdo->query($query);
}

$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h1>Job Applications</h1>
    
    <div class="mb-3">
        <label for="statusFilter" class="form-label">Filter by Status:</label>
        <select id="statusFilter" class="form-select" onchange="filterStatus()">
            <option value="all" <?php if ($status === 'all') echo 'selected'; ?>>All</option>
            <option value="applied" <?php if ($status === 'applied') echo 'selected'; ?>>Applied</option>
            <option value="reviewed" <?php if ($status === 'reviewed') echo 'selected'; ?>>Reviewed</option>
            <option value="interview" <?php if ($status === 'interview') echo 'selected'; ?>>Interview</option>
            <option value="hired" <?php if ($status === 'hired') echo 'selected'; ?>>Hired</option>
            <option value="rejected" <?php if ($status === 'rejected') echo 'selected'; ?>>Rejected</option>
        </select>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Job Title</th>
                <th>Applicant Email</th>
                <th>Status</th>
                <th>Applied At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['id']); ?></td>
                    <td><?php echo htmlspecialchars($application['title']); ?></td>
                    <td><?php echo htmlspecialchars($application['email']); ?></td>
                    <td><?php echo htmlspecialchars($application['status']); ?></td>
                    <td><?php echo htmlspecialchars($application['applied_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function filterStatus() {
    var status = document.getElementById('statusFilter').value;
    window.location.href = '?status=' + status;
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>