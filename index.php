<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get featured jobs (active jobs with nearest deadlines)
$stmt = $pdo->query("
    SELECT * FROM jobs 
    WHERE is_active = TRUE AND deadline >= CURDATE()
    ORDER BY deadline ASC
    LIMIT 3
");
$featured_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total counts for stats
$total_jobs = $pdo->query("SELECT COUNT(*) FROM jobs WHERE is_active = TRUE")->fetchColumn();
$total_companies = $pdo->query("SELECT COUNT(DISTINCT posted_by) FROM jobs")->fetchColumn();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Find Your Dream Job Today</h1>
                <p class="lead mb-4">Join thousands of professionals who found their perfect career match through our platform.</p>
                <div class="d-flex gap-3">
                    <a href="jobs/index.php" class="btn btn-light btn-lg px-4">Browse Jobs</a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="auth/register.php" class="btn btn-outline-light btn-lg px-4">Register Now</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/hero-image.jpg" alt="Career growth" class="img-fluid d-none d-lg-block">
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary"><?php echo $total_jobs; ?></div>
                <p class="fs-5 text-muted">Available Jobs</p>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary"><?php echo $total_companies; ?></div>
                <p class="fs-5 text-muted">Partner Companies</p>
            </div>
            <div class="col-md-4">
                <div class="display-4 fw-bold text-primary">100%</div>
                <p class="fs-5 text-muted">Free Service</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold">Featured Jobs</h2>
            <a href="jobs/index.php" class="btn btn-outline-primary">View All Jobs</a>
        </div>
        
        <?php if (count($featured_jobs) > 0): ?>
            <div class="row g-4">
                <?php foreach ($featured_jobs as $job): ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?php echo htmlspecialchars($job['department'] ?? 'General'); ?>
                                </h6>
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($job['location']); ?>
                                    </span>
                                    <span class="badge bg-light text-dark ms-2">
                                        <i class="bi bi-cash"></i> <?php echo htmlspecialchars($job['salary_range'] ?? 'Negotiable'); ?>
                                    </span>
                                </div>
                                <p class="card-text text-truncate">
                                    <?php echo htmlspecialchars($job['description']); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                                    </small>
                                    <a href="jobs/view.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">Apply Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No featured jobs available at the moment. Please check back later.</div>
        <?php endif; ?>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">How It Works</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-plus fs-3"></i>
                    </div>
                    <h5>Create Your Profile</h5>
                    <p>Register an account and complete your professional profile with your skills and resume.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-search fs-3"></i>
                    </div>
                    <h5>Find Suitable Jobs</h5>
                    <p>Browse through hundreds of job listings and find the ones that match your skills.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-send-check fs-3"></i>
                    </div>
                    <h5>Apply With One Click</h5>
                    <p>Submit your application with your pre-filled profile and track your application status.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold">What Our Users Say</h2>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center p-5">
                                    <img src="assets/images/testimonial-1.png" class="rounded-circle mb-4" width="100" alt="User">
                                    <p class="lead mb-4">"I found my dream job within two weeks of using this platform. The application process was so simple!"</p>
                                    <h5 class="mb-1">Sarah Johnson</h5>
                                    <p class="text-muted">Marketing Manager at TechCorp</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center p-5">
                                    <img src="assets/images/testimonial-2.jpg" class="rounded-circle mb-4" width="100" alt="User">
                                    <p class="lead mb-4">"As a recruiter, this platform has helped me find quality candidates quickly and efficiently."</p>
                                    <h5 class="mb-1">Michael Chen</h5>
                                    <p class="text-muted">HR Director at InnovateSoft</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Ready to take the next step in your career?</h2>
        <p class="lead mb-5">Join thousands of professionals who found their perfect match through our platform.</p>
        <a href="<?php echo isLoggedIn() ? 'jobs/index.php' : 'auth/register.php'; ?>" class="btn btn-light btn-lg px-5">
            <?php echo isLoggedIn() ? 'Browse Jobs' : 'Get Started Now'; ?>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>