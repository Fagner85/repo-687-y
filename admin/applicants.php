<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header('Location: ../auth/login.php');
    exit();
}

// Search and filter functionality
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Base query
$query = "SELECT u.id, u.email, ap.first_name, ap.last_name, 
          COUNT(a.id) as application_count,
          MAX(a.applied_at) as last_application
          FROM users u
          LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
          LEFT JOIN applications a ON u.id = a.user_id
          WHERE u.role = 'applicant'";

$params = [];

// Add search conditions
if (!empty($search)) {
    $query .= " AND (u.email LIKE ? OR ap.first_name LIKE ? OR ap.last_name LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
}

// Group and order
$query .= " GROUP BY u.id
            ORDER BY last_application DESC";

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Applicant Management</h2>
    
    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="applicants.php" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Applicants Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Applications</th>
                            <th>Last Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applicants as $applicant): ?>
                            <tr>
                                <td><?= $applicant['id'] ?></td>
                                <td><?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?></td>
                                <td><?= htmlspecialchars($applicant['email']) ?></td>
                                <td><?= $applicant['application_count'] ?></td>
                                <td><?= $applicant['last_application'] ? date('M d, Y', strtotime($applicant['last_application'])) : 'Never' ?></td>
                                <td>
                                    <a href="applicant_view.php?id=<?= $applicant['id'] ?>" class="btn btn-sm btn-primary">View</a>
                                    <a href="applicant_applications.php?user_id=<?= $applicant['id'] ?>" class="btn btn-sm btn-info">Applications</a>
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