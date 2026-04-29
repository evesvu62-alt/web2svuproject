<?php
declare(strict_types=1);

require_once __DIR__ . '/nav_footer.php';

renderSiteHeader('About Us - UNI Events', 'about');
?>


<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="about-hero-content text-center">
            <h1 class="display-4 fw-bold mb-4 about-hero-title">About UNI Events</h1>
            <p class="lead mb-4 mx-auto about-hero-lead">
                your gateway to university life and campus experiences
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap about-hero-badges">
                <span class="badge rounded-pill text-bg-info px-3 py-2">community driven</span>
                <span class="badge rounded-pill text-bg-success px-3 py-2">student focused</span>
                <span class="badge rounded-pill text-bg-warning text-dark px-3 py-2">always updated</span>
            </div>
        </div>
    </div>
</section>

<!-- Goal and Vision Section -->
<section class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0 about-intro-copy">
                <h2 class="h2 mb-3 text-info">Our Vision</h2>
                <p class="lead mb-4">
                    We want every student to feel connected to campus life, not left out of it.
                </p>
                <h3 class="h4 mb-3 text-success">Our Mission</h3>
                <p class="mb-0">
                    We make it simple to discover, share, and join meaningful university events in one friendly place.
                </p>
            </div>
            <div class="col-lg-6 about-values-col">
                <h3 class="h4 mb-4 text-success">What We Stand For</h3>
                <div class="row g-3 about-values-grid">
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-people text-white"></i>
                            </div>
                            <div>
                                <h5 class="h6 mb-0">Community</h5>
                                <small class="text-body-secondary">Building connections</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-calendar-event text-white"></i>
                            </div>
                            <div>
                                <h5 class="h6 mb-0">Events</h5>
                                <small class="text-body-secondary">Unforgettable experiences</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-star text-dark"></i>
                            </div>
                            <div>
                                <h5 class="h6 mb-0">Quality</h5>
                                <small class="text-body-secondary">Curated content</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-shield-check text-white"></i>
                            </div>
                            <div>
                                <h5 class="h6 mb-0">Trust</h5>
                                <small class="text-body-secondary">Safe environment</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team and Partners Section -->
<section class="team-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h2 mb-3 text-info">Team and Partners</h2>
            <p class="lead text-body-secondary">Meet the individuals behind UNI Events</p>
        </div>
        
        <div class="row g-4">
            <!-- Team Member 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="team-card p-4 text-center">
                    <div class="team-avatar">GA</div>
                    <h3 class="team-name">Ghassan Assaf</h3>
                    <div class="team-id">Ghassan_284782</div>
                    <p class="team-role">Founder & Lead Developer</p>
                </div>
            </div>
            
            <!-- Team Member 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="team-card p-4 text-center">
                    <div class="team-avatar">AW</div>
                    <h3 class="team-name">Ammar Wanoos</h3>
                    <div class="team-id">ammar_265955</div>
                    <p class="team-role">Community Manager</p>
                </div>
            </div>
            
            <!-- Team Member 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="team-card p-4 text-center">
                    <div class="team-avatar">MAA</div>
                    <h3 class="team-name">mohamad abou assaf</h3>
                    <div class="team-id">mohammad_284833</div>
                    <p class="team-role">UX Designer</p>
                </div>
            </div>
            
            <!-- Team Member 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="team-card p-4 text-center">
                    <div class="team-avatar">HI</div>
                    <h3 class="team-name">Homam Ibraheem</h3>
                    <div class="team-id">homam_283223</div>
                    <p class="team-role">Backend Engineer</p>
                </div>
            </div>
            
            <!-- Team Member 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="team-card p-4 text-center">
                    <div class="team-avatar">BA</div>
                    <h3 class="team-name">Basel Addounia </h3>
                    <div class="team-id">basel_306749</div>
                    <p class="team-role">Marketing Lead</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Policies Section -->
<section class="policies-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h2 mb-3 text-info">Event Submission Policies</h2>
            <p class="lead text-body-secondary">Guidelines to ensure quality and safety for our community</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="policy-card">
                    <h3 class="policy-title">
                        <i class="bi bi-shield-check me-2"></i>
                        Content Guidelines
                    </h3>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li>All events must be university related or student focused</li>
                            <li>No commercial advertising or promotional content</li>
                            <li>Events must be appropriate for all university students</li>
                            
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="policy-card">
                    <h3 class="policy-title">
                        <i class="bi bi-calendar-check me-2"></i>
                        Event Requirements
                    </h3>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li>Events must have a clear purpose and target audience</li>
                            <li>Valid contact information is mandatory</li>
                            <li>Events must be scheduled at least 48 hours in advance</li>
                            
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="policy-card">
                    <h3 class="policy-title">
                        <i class="bi bi-people me-2"></i>
                        Community Standards
                    </h3>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li>Respect for all participants is non-negotiable</li>
                            <li>Events must be inclusive and welcoming to all students</li>
                            <li>Organizers must moderate their events appropriately</li>
                            
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="policy-card">
                    <h3 class="policy-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Enforcement & Violations
                    </h3>
                    <div class="policy-content">
                        <ul class="policy-list">
                            <li>Violations may result in event removal or account suspension</li>
                            <li>Repeated violations may lead to permanent bans</li>
                            <li>False or misleading information is strictly prohibited</li>
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p class="text-body-secondary">
                <small>Questions about policies? <a href="contact.php" class="text-info">Contact Us</a></small>
            </p>
        </div>
    </div>
</section>

<?php renderSiteFooter(); ?>