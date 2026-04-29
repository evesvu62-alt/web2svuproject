<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/nav_footer.php';

$events = [];
$categories = [];
$selectedCategory = '';

// Get category from URL parameter (from quick category buttons)
$urlCategory = $_GET['category'] ?? '';
if ($urlCategory !== '') {
	$selectedCategory = htmlspecialchars($urlCategory);
}

try {
	// If category is selected, filter the events
	if ($selectedCategory !== '') {
		$eventsStmt = $pdo->prepare('SELECT id, title, description, category, location, event_date, image FROM events WHERE category = :category ORDER BY event_date DESC, id DESC');
		$eventsStmt->execute(['category' => $selectedCategory]);
		$events = $eventsStmt->fetchAll();
	} else {
		$eventsStmt = $pdo->query('SELECT id, title, description, category, location, event_date, image FROM events ORDER BY event_date DESC, id DESC');
		$events = $eventsStmt->fetchAll();
	}

	$categoryStmt = $pdo->query('SELECT DISTINCT category FROM events WHERE category IS NOT NULL AND category <> "" ORDER BY category ASC');
	$categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
	$events = [];
	$categories = [];
}

renderSiteHeader('UNI Events', 'events');
?>

<!-- events page header -->
<section class="mb-4">
	<div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
		<div>
			<h1 class="h2 mb-1">Discover Events</h1>
			<p class="text-body-secondary mb-0">Browse UNIversity city happenings and filter results in real time</p>
			<?php if ($selectedCategory !== ''): ?>
				<div class="mt-2">
					<span class="badge rounded-pill text-bg-info me-2">Category: <?= htmlspecialchars($selectedCategory) ?></span>
					<a href="events.php" class="badge rounded-pill text-bg-secondary text-decoration-none">clear filter</a>
				</div>
			<?php endif; ?>
		</div>
		<span class="badge rounded-pill event-count-pill px-3 py-2" id="eventCountLabel"><?= count($events) ?> events</span>
	</div>
</section>

<!-- events grid -->
<section>
	<div class="row g-4 align-items-start">
		<div class="col-md-3">
			<aside class="events-sidebar p-3 p-lg-4 rounded-4 sticky-md-top" style="top: 5.5rem;">
				<h2 class="h6 text-uppercase fw-semibold mb-3">Filter Events</h2>

				<div class="mb-3">
					<label for="eventsSearch" class="form-label small fw-semibold">Search</label>
					<input type="search" id="eventsSearch" class="form-control form-control-sm events-filter-input" placeholder="Search title or location">
				</div>

				<div class="mb-3">
					<label for="eventsCategory" class="form-label small fw-semibold">Category</label>
					<select id="eventsCategory" class="form-select form-select-sm events-filter-input">
						<option value="">All categories</option>
						<?php foreach ($categories as $category): ?>
							<?php $categoryText = (string) $category; ?>
							<option value="<?= htmlspecialchars(strtolower($categoryText)) ?>" <?= $selectedCategory === $categoryText ? 'selected' : '' ?>><?= htmlspecialchars($categoryText) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="mb-3">
					<label for="eventsDate" class="form-label small fw-semibold">date</label>
					<input type="date" id="eventsDate" class="form-control form-control-sm events-filter-input">
				</div>

				<button type="button" id="eventsReset" class="btn btn-sm btn-outline-info rounded-pill w-100">reset filters</button>
			</aside>
		</div>

		<div class="col-md-9">
			<?php if ($events !== []): ?>
				<div class="events-masonry" id="eventsMasonry">
					<!-- event card -->
					<?php foreach ($events as $event): ?>
						<?php
						$id = (int) $event['id'];
						$titleRaw = (string) ($event['title'] ?? 'Untitled event');
						$descriptionRaw = (string) ($event['description'] ?? 'No description available');
						$categoryRaw = (string) ($event['category'] ?? 'General');
						$locationRaw = (string) ($event['location'] ?? 'Location not set');
						$eventDateRaw = (string) ($event['event_date'] ?? '');
						$eventDateDisplay = $eventDateRaw !== '' ? date('M d, Y', strtotime($eventDateRaw)) : 'Date not set';
						$imageName = (string) ($event['image'] ?? '');
						$imagePath = $imageName !== '' ? 'uploads/' . rawurlencode($imageName) : 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=900&q=80';
						?>
						<article class="event-masonry-item" data-event-card data-title="<?= htmlspecialchars(strtolower($titleRaw)) ?>" data-location="<?= htmlspecialchars(strtolower($locationRaw)) ?>" data-category="<?= htmlspecialchars(strtolower($categoryRaw)) ?>" data-date="<?= htmlspecialchars($eventDateRaw) ?>">
							<div class="card event-grid-card h-100 border-0 overflow-hidden">
								<img src="<?= $imagePath ?>" class="card-img-top event-grid-image" alt="<?= htmlspecialchars($titleRaw) ?>">
								<div class="card-body d-flex flex-column">
									<div class="d-flex justify-content-between align-items-center gap-2 mb-2">
										<span class="badge rounded-pill text-bg-info"><?= htmlspecialchars($categoryRaw) ?></span>
										<small class="text-body-secondary"><?= htmlspecialchars($eventDateDisplay) ?></small>
									</div>
									<h3 class="h5 mb-2"><?= htmlspecialchars($titleRaw) ?></h3>
									<p class="text-body-secondary small mb-3"><?= htmlspecialchars($locationRaw) ?></p>
									<p class="card-text text-body-secondary mb-3"><?= htmlspecialchars(mb_strimwidth($descriptionRaw, 0, 170, '...')) ?></p>
									<a href="event.php?id=<?= $id ?>" class="mt-auto fw-semibold text-decoration-none">read more</a>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
				<p id="eventsEmptyState" class="alert glass-alert border-0 mt-3 d-none mb-0">no events match your filters</p>
			<?php else: ?>
				<div class="alert glass-alert border-0 mb-0">no events found add events from the admin panel to see them here</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php renderSiteFooter(); ?>
