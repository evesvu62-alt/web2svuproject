<?php
declare(strict_types=1);

require_once __DIR__ . '/nav_footer.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Basic server-side validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Mock submission 
        $success = 'Thank you for your message! We\'ll get back to you within 24 hours.';
        
        // Clear form fields
        $name = '';
        $email = '';
        $message = '';
    }
}

renderSiteHeader('Contact Us - UNI Events', 'contact');
?>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="contact-form-container">
                    <h2 class="h3 mb-4">get in touch</h2>
                    <p class="text-body-secondary mb-4">have a question or feedback? <br> we'd love to hear from you, send us a message</p>
                    
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success !== ''): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contact.php" id="contactForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label fw-semibold">Name *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="name" 
                                           name="name" 
                                           placeholder="Your full name"
                                           value="<?= htmlspecialchars($name ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter your name
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label fw-semibold">Email *</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           placeholder="your.email@example.com"
                                           value="<?= htmlspecialchars($email ?? '') ?>"
                                           required>
                                    <div class="invalid-feedback">
                                        please enter a valid email address
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="message" class="form-label fw-semibold">Message *</label>
                                    <textarea class="form-control" 
                                              id="message" 
                                              name="message" 
                                              rows="6" 
                                              placeholder="Tell us what's on your mind..."
                                              required><?= htmlspecialchars($message ?? '') ?></textarea>
                                    <div class="invalid-feedback">
                                        please enter your message
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i>
                                    send message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="contact-info">
                    <h3 class="h4 mb-4">other ways to reach us</h3>
                    
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="contact-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-semibold mb-1">Email</h4>
                                <p class="text-body-secondary mb-0">svu@svu.edu</p>
                                <small class="text-body-secondary">we respond within 24 hours</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="contact-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-semibold mb-1">office</h4>
                                <p class="text-body-secondary mb-0">123 UNI Street<br>City Center</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="contact-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-semibold mb-1">business hours</h4>
                                <p class="text-body-secondary mb-0">monday-friday: 9am - 6pm<br>weekend: closed</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <h4 class="h6 fw-semibold mb-3">Follow Us</h4>
                        <div class="d-flex gap-3">
                            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-link">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="social-link">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-link">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="social-link">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php renderSiteFooter(); ?>