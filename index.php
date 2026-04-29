<?php

// welcome page
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/nav_footer.php';

// get recent events
$recentEvents = [];

try {
	$recentStmt = $pdo->query('SELECT id, title, description, category, location, event_date, image FROM events ORDER BY id DESC LIMIT 3');
	$recentEvents = $recentStmt->fetchAll();
} catch (PDOException $e) {
	$recentEvents = [];
}

renderSiteHeader('UNI Events Dashboard', 'home');
?>

<!-- hero section -->
<section class="hero-banner rounded-4 overflow-hidden mb-5 position-relative">
	<div class="hero-overlay"></div>
	<div class="hero-content p-4 p-md-5 position-relative text-white">
		<span class="badge rounded-pill text-bg-light text-dark px-3 py-2 mb-3">UNI Events Dashboard</span>
		<h1 class="display-5 fw-bold mb-3 hero-title">Find your next unforgettable university moment</h1>
		<p class="lead mb-4 col-lg-8 hero-lead">Discover fresh local happenings from music nights and culture festivals to sports matches and tech meetups</p>
		<div class="d-flex flex-wrap gap-2 mb-4 hero-tag-row">
			<span class="hero-mini-tag">Live updates</span>
			<span class="hero-mini-tag">Campus first</span>
			<span class="hero-mini-tag">Student made</span>
		</div>
		<div class="d-flex flex-wrap align-items-center gap-3">
			<a href="events.php" class="btn btn-lg btn-info rounded-pill px-4 fw-semibold">Explore All Events</a>
			<a href="about.php" class="btn btn-lg btn-outline-light rounded-pill px-4 fw-semibold">Learn More</a>
		</div>
	</div>
</section>

<!-- quick filters -->
<section class="mb-5 quick-filter-wrap">
	<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 section-head-row">
		<h2 class="h4 mb-0 section-title">Quick Filters</h2>
		<small class="text-body-secondary">jump to your favorite category</small>
	</div>
	<div class="d-flex flex-wrap gap-2 quick-pill-grid">
		<a class="btn btn-sm rounded-pill quick-pill" href="events.php?category=Culture">Culture</a>
		<a class="btn btn-sm rounded-pill quick-pill" href="events.php?category=Tech">Tech</a>
		<a class="btn btn-sm rounded-pill quick-pill" href="events.php?category=Sports">Sports</a>
		<a class="btn btn-sm rounded-pill quick-pill" href="events.php?category=Music">Music</a>
	</div>
</section>

<!-- Team and Partners Section -->
<section class="team-section py-4">
    <div class="container">
		<div class="text-center mb-4 section-head-row">
			<h2 class="h3 text-info section-title">Team and Partners</h2>
            <p class="text-body-secondary">Meet the individuals behind UNI Events</p>
        </div>
        
        <div class="row g-3">
            <!-- Team Member 1 -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card p-2 text-center">
                    <div class="team-avatar mb-2">GA</div>
                    <h3 class="team-name h6 mb-0">Ghassan Assaf</h3>
                    <div class="team-id small text-muted">Ghassan_284782</div>
                    <p class="team-role small mb-0">Founder & Lead Dev</p>
                </div>
            </div>
            
            <!-- Team Member 2 -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card p-2 text-center">
                    <div class="team-avatar mb-2">AW</div>
                    <h3 class="team-name h6 mb-0">Ammar Wanoos</h3>
                    <div class="team-id small text-muted">ammar_265955</div>
                    <p class="team-role small mb-0">Community Manager</p>
                </div>
            </div>
            
            <!-- Team Member 3 -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card p-2 text-center">
                    <div class="team-avatar mb-2">MAA</div>
                    <h3 class="team-name h6 mb-0">mohamad abou assaf</h3>
                    <div class="team-id small text-muted">mohammad_284833</div>
                    <p class="team-role small mb-0">UX Designer</p>
                </div>
            </div>
            
            <!-- Team Member 4 -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card p-2 text-center">
                    <div class="team-avatar mb-2">HI</div>
                    <h3 class="team-name h6 mb-0">Homam Ibraheem</h3>
                    <div class="team-id small text-muted">homam_283223</div>
                    <p class="team-role small mb-0">Backend Engineer</p>
                </div>
            </div>
            
            <!-- Team Member 5 -->
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card p-2 text-center">
                    <div class="team-avatar mb-2">BA</div>
                    <h3 class="team-name h6 mb-0">Basel Addounia</h3>
                    <div class="team-id small text-muted">basel_306749</div>
                    <p class="team-role small mb-0">Marketing Lead</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- recently added -->
<section class="recent-wrap">
	<div class="d-flex justify-content-between align-items-center gap-3 mb-3 section-head-row">
		<h2 class="h4 mb-0 section-title">Recently Added</h2>
		<a href="events.php" class="link-primary text-decoration-none small fw-semibold">see all events</a>
	</div>

	<?php if ($recentEvents !== []): ?>
		<div class="d-flex flex-column gap-3">
			<?php foreach ($recentEvents as $event): ?>
				<?php
				$title = htmlspecialchars((string) $event['title']);
				$description = htmlspecialchars((string) ($event['description'] ?? 'No description available'));
				$category = htmlspecialchars((string) ($event['category'] ?? 'General'));
				$location = htmlspecialchars((string) ($event['location'] ?? 'Location not set'));
				$imageName = (string) ($event['image'] ?? '');
				$imagePath = $imageName !== '' ? 'uploads/' . rawurlencode(string: $imageName) : 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=900&q=80';
				$eventDate = !empty($event['event_date']) ? date('M d, Y', strtotime((string) $event['event_date'])) : 'Date not set';
				?>
				<!-- recent event card -->
				<article class="card recent-card border-0 shadow-sm overflow-hidden recent-card-home">
					<div class="row g-0 align-items-stretch">
						<div class="col-md-4 recent-card-media">
							<img src="<?= $imagePath ?>" class="img-fluid w-100 h-100 object-fit-cover" alt="<?= $title ?>">
						</div>
						<div class="col-md-8">
							<div class="card-body p-4 d-flex flex-column h-100">
								<div class="d-flex flex-wrap align-items-center gap-2 mb-2">
									<span class="badge rounded-pill text-bg-info"><?= $category ?></span>
									<span class="text-body-secondary small"><?= htmlspecialchars($eventDate) ?></span>
								</div>
								<h3 class="h5 card-title mb-2"><?= $title ?></h3>
								<p class="card-text text-body-secondary mb-3"><?= mb_strimwidth($description, 0, 180, '...') ?></p>
								<div class="mt-auto d-flex justify-content-between align-items-center">
									<span class="small text-body-secondary"><?= $location ?></span>
									<a href="event.php?id=<?= (int) $event['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">View Event</a>
								</div>
							</div>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<!-- no events message -->
		<div class="alert glass-alert border-0 mb-0" role="alert">
			No events yet Add your first event from the admin panel and it will appear here
		</div>
	<?php endif; ?>
</section>

<?php renderSiteFooter(); ?>
