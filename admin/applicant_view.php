<?php
require_once __DIR__ . '/../includes/config.php';

// Ensure the user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('/auth/login.php');
}

// Get the applicant ID from the query parameter
$applicantId = $_GET['id'] ?? null;

if ($applicantId === null) {
    die('Applicant ID is required');
}

// Fetch applicant details from the database
$stmt = $pdo->prepare("SELECT users.id, users.email, users.role, users.created_at FROM users WHERE users.id = ?");
$stmt->execute([$applicantId]);
$applicant = $stmt->fetch();

if (!$applicant) {
    die('Applicant not found');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h1>Applicant Details</h1>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4><?php echo htmlspecialchars($applicant['email']); ?></h4>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($applicant['id']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($applicant['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($applicant['role']); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($applicant['created_at']); ?></p>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include '../includes/footer.php'; ?>
</body>
</html>