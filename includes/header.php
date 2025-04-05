<?php
// Ensure config is loaded (which loads functions)
if (!isset($pdo)) {
    require_once __DIR__ . '/config.php';
}

// Verify authentication status
$isLoggedIn = isLoggedIn();
$isAdmin = isAdmin();
$isApplicant = isApplicant();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">JobApp</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/jobs/">Jobs</a>
                    </li>
                    <?php if ($isApplicant): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/applicant/dashboard.php">Dashboard</a>
                        </li>
                    <?php elseif ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/dashboard.php">Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container my-4">