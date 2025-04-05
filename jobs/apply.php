<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isApplicant()) {
    redirect('../auth/login.php');
}

$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

// Get job details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND is_active = TRUE AND deadline >= CURDATE()");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    $_SESSION['error'] = "Job not found or expired";
    redirect('index.php');
}

$user_id = getUserId();
$profile = getApplicantProfile($user_id);

// Check if already applied
$stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND user_id = ?");
$stmt->execute([$job_id, $user_id]);
$already_applied = $stmt->fetch(PDO::FETCH_ASSOC);

if ($already_applied) {
    $_SESSION['error'] = "You have already applied for this job";
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if profile is complete
    if (!$profile || empty($profile['resume_path'])) {
        $error = "Please complete your profile and upload a resume before applying";
    } else {
        // Submit application
        $stmt = $pdo->prepare("INSERT INTO applications (job_id, user_id) VALUES (?, ?)");
        $stmt->execute([$job_id, $user_id]);
        
        $_SESSION['success'] = "Application submitted successfully";
        redirect('../applicant/dashboard.php');
    }
}

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Apply for: <?php echo htmlspecialchars($job['title']); ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <h5>Job Details</h5>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($job['department'] ?? 'General'); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                        <p><strong>Salary Range:</strong> <?php echo htmlspecialchars($job['salary_range'] ?? 'Negotiable'); ?></p>
                        <p><strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($job['deadline'])); ?></p>
                        <div class="mt-3">
                            <h6>Description:</h6>
                            <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Your Application</h5>
                        <?php if ($profile): ?>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars(getUserById($user_id)['email']); ?></p>
                            <p><strong>Resume:</strong> 
                                <?php if (!empty($profile['resume_path'])): ?>
                                    <a href="../<?php echo htmlspecialchars($profile['resume_path']); ?>" target="_blank">View Resume</a>
                                <?php else: ?>
                                    <span class="text-danger">No resume uploaded</span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST">
                        <div class="alert alert-info">
                            By submitting this application, you agree that the information you've provided is accurate.
                        </div>
                        
                        <?php if (!$profile || empty($profile['resume_path'])): ?>
                            <div class="alert alert-warning">
                                Please <a href="../applicant/profile.php">complete your profile</a> and upload a resume before applying.
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">Back to Jobs</a>
                            <?php if ($profile && !empty($profile['resume_path'])): ?>
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" disabled>Submit Application</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>