<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure the user is logged in
if (!isLoggedIn()) {
    redirect('/auth/login.php');
}

// Get the applicant's profile
$userId = $_SESSION['user_id'];
$applicantProfile = getApplicantProfile($userId, $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h1>Welcome!</h1>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Your Profile</h4>
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($applicantProfile['email']); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($applicantProfile['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($applicantProfile['last_name']); ?></p>
            <p><strong>Joined At:</strong> <?php echo htmlspecialchars($applicantProfile['created_at']); ?></p>
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