<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Search and filter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$department = isset($_GET['department']) ? sanitize($_GET['department']) : '';
$location = isset($_GET['location']) ? sanitize($_GET['location']) : '';

// Build query
$query = "SELECT * FROM jobs WHERE is_active = TRUE AND deadline >= CURDATE()";
$params = [];

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($department)) {
    $query .= " AND department = ?";
    $params[] = $department;
}

if (!empty($location)) {
    $query .= " AND location LIKE ?";
    $params[] = "%$location%";
}

$query .= " ORDER BY posted_at DESC";

// Get jobs
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique departments for filter
$departments = $pdo->query("SELECT DISTINCT department FROM jobs WHERE department IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">Keywords</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo htmlspecialchars($dept); ?>" 
                                        <?php echo $department === $dept ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                value="<?php echo htmlspecialchars($location); ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="index.php" class="btn btn-secondary">Reset</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <h2>Available Jobs</h2>
            
            <?php if (count($jobs) > 0): ?>
                <div class="row">
                    <?php foreach ($jobs as $job): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        <?php echo htmlspecialchars($job['department'] ?? 'General'); ?> • 
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </h6>
                                    <p class="card-text">
                                        <?php echo substr(htmlspecialchars($job['description']), 0, 150); ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-info text-dark">
                                            Salary: <?php echo htmlspecialchars($job['salary_range'] ?? 'Negotiable'); ?>
                                        </span>
                                        <span class="text-muted small">
                                            Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="view.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No jobs found matching your criteria.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>