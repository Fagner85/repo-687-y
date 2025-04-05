<?php
require_once __DIR__ . '/../includes/config.php';

// Force authentication
if (!isApplicant()) {
    redirect('../auth/login.php');
}

// Get current user ID
$user_id = getUserId();
if (!$user_id) {
    logoutUser();
    redirect('../auth/login.php');
}

// Get applicant profile
$profile = getApplicantProfile($user_id);
if (!$profile) {
    // Handle case where profile doesn't exist
    $profile = [];
}

// Your dashboard content starts here
?>

<!-- HTML Content -->
<div class="container">
    <h2>Welcome, <?= htmlspecialchars($profile['first_name'] ?? 'Applicant') ?></h2>
    <!-- Rest of your dashboard -->
</div>