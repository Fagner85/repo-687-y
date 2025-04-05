</main>

<footer class="bg-dark text-white mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-6">
                <h5>Job Application System</h5>
                <p>A simple yet powerful job application management system.</p>
            </div>
            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="../jobs/index.php" class="text-white">Browse Jobs</a></li>
                    <?php if (!isLoggedIn()): ?>
                        <li><a href="../auth/login.php" class="text-white">Login</a></li>
                        <li><a href="../auth/register.php" class="text-white">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact</h5>
                <ul class="list-unstyled">
                    <li>Email: info@jobapp.com</li>
                    <li>Phone: (123) 456-7890</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center py-3 bg-secondary">
        &copy; <?php echo date('Y'); ?> Job Application System
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>