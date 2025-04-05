<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isApplicant()) {
    redirect('../auth/login.php');
}

$user_id = getUserId();
$profile = getApplicantProfile($user_id);
$user = getUserById($user_id);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $skills = sanitize($_POST['skills']);
    
    // Handle resume upload
    $resume_path = $profile['resume_path'] ?? null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadResume($_FILES['resume']);
        if ($upload['success']) {
            $resume_path = $upload['path'];
        } else {
            $error = $upload['message'];
        }
    }
    
    if (empty($error)) {
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("
                UPDATE applicant_profiles 
                SET first_name = ?, last_name = ?, phone = ?, address = ?, skills = ?, resume_path = ?
                WHERE user_id = ?
            ");
            $stmt->execute([$first_name, $last_name, $phone, $address, $skills, $resume_path, $user_id]);
        } else {
            // Create new profile
            $stmt = $pdo->prepare("
                INSERT INTO applicant_profiles 
                (user_id, first_name, last_name, phone, address, skills, resume_path)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$user_id, $first_name, $last_name, $phone, $address, $skills, $resume_path]);
        }
        
        $success = 'Profile updated successfully';
        $profile = getApplicantProfile($user_id); // Refresh profile data
    }
}

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Your Profile</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                    value="<?php echo $profile['first_name'] ?? ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                    value="<?php echo $profile['last_name'] ?? ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" 
                                value="<?php echo $user['email']; ?>" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                value="<?php echo $profile['phone'] ?? ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?php echo $profile['address'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills (comma separated)</label>
                            <textarea class="form-control" id="skills" name="skills" rows="3"><?php echo $profile['skills'] ?? ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="resume" class="form-label">Resume (PDF, DOC, DOCX)</label>
                            <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                            <?php if (isset($profile['resume_path']) && !empty($profile['resume_path'])): ?>
                                <div class="mt-2">
                                    <a href="../<?php echo $profile['resume_path']; ?>" target="_blank" class="btn btn-sm btn-success">View Current Resume</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 
