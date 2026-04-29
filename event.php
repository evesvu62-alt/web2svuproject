<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/nav_footer.php';
require_once __DIR__ . '/event_share.php';

$event = null;
$relatedEvents = [];

// Get event ID from URL parameter
$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($eventId > 0) {
    try {
        // fetch the main event
        
        $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute(['id' => $eventId]);
        $event = $stmt->fetch();
        
        if ($event && !empty($event['category'])) {
            // fetch 2 random events from the same category (excluding current event)
            $relatedStmt = $pdo->prepare('
                SELECT id, title, description, category, location, event_date, image 
                FROM events 
                WHERE category = :category AND id != :id 
                ORDER BY RAND() 
                LIMIT 2
            ');
            $relatedStmt->execute([
                'category' => $event['category'],
                'id' => $eventId
            ]);
            $relatedEvents = $relatedStmt->fetchAll();
        }
    } catch (PDOException $e) {
        $event = null;
        $relatedEvents = [];
    }
}

// if no event found, show error page
if (!$event) {
    header('HTTP/1.0 404 Not Found');
    echo '<!DOCTYPE html><html><head><title>Event Not Found</title></head><body><h1>Event Not Found</h1><p>The requested event could not be found.</p><a href="events.php">Browse Events</a></body></html>';
    exit;
}

// prepare variables for display
$title = htmlspecialchars((string) $event['title']);
$description = htmlspecialchars((string) ($event['description'] ?? 'No description available'));
$category = htmlspecialchars((string) ($event['category'] ?? 'General'));
$location = htmlspecialchars((string) ($event['location'] ?? 'Location not set'));
$eventDate = !empty($event['event_date']) ? date('F d, Y', strtotime((string) $event['event_date'])) : 'Date not set';
$eventDateForCalendar = !empty($event['event_date']) ? date('Ymd', strtotime((string) $event['event_date'])) : '';
$imageName = (string) ($event['image'] ?? '');
$imagePath = $imageName !== '' ? 'uploads/' . rawurlencode($imageName) : 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80';

// generate share URL
$shareUrl = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$shareTitle = urlencode($title);

renderSiteHeader($title, 'events');

?>
<!-- Cover Image Header -->
<section class="event-header position-relative overflow-hidden">
    <img src="<?= $imagePath ?>" class="event-header-image position-absolute w-100 h-100 object-fit-cover" alt="<?= $title ?>">
    <div class="event-header-overlay position-absolute w-100 h-100"></div>
    <div class="container position-relative">
        <div class="event-header-content text-white py-5">
            <div class="row align-items-center" style="min-height: 400px;">
                <div class="col-lg-8">
                    <div class="event-header-text">
                        <span class="badge rounded-pill text-bg-info mb-3"><?= $category ?></span>
                        <h1 class="display-4 fw-bold mb-3"><?= $title ?></h1>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V4H1z"/>
                                </svg>
                                <span class="fs-5"><?= $eventDate ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="container py-5">
    <div class="row g-5">
        <!-- Left Column - Full Description -->
        <div class="col-lg-8">
            <div class="event-description">
                <h2 class="h3 mb-4">About This Event</h2>
                <div class="prose">
                    <p class="fs-5"><?= nl2br($description) ?></p>
                </div>
            </div>
        </div>

        <!-- Right Column - Sticky Sidebar -->
        <div class="col-lg-4">
            <aside class="event-sidebar">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Location -->
                        <div class="mb-4">
                            <h3 class="h6 text-uppercase fw-semibold mb-2">Location</h3>
                            <div class="d-flex align-items-start gap-2">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="mt-1">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                </svg>
                                <span><?= $location ?></span>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <h3 class="h6 text-uppercase fw-semibold mb-2">Category</h3>
                            <span class="badge rounded-pill text-bg-info"><?= $category ?></span>
                        </div>

                        <!-- Add to Calendar Button -->
                        <div class="mb-4">
                            <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?= $shareTitle ?>&dates=<?= $eventDateForCalendar ?>/<?= $eventDateForCalendar ?>&details=<?= urlencode($description) ?>&location=<?= urlencode($location) ?>" 
                               class="btn btn-primary w-100 rounded-pill" 
                               target="_blank" 
                               rel="noopener noreferrer">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V4H1z"/>
                                </svg>
                                add to calendar
                            </a>
                        </div>

                        <!-- Share Button -->
                        <div class="mb-0">
                            <button type="button" 
                                    class="btn btn-outline-info w-100 rounded-pill" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#shareModal">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="me-2">
                                    <path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>
                                </svg>
                                share event
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- You Might Also Like Section -->
<?php if (!empty($relatedEvents)): ?>
<section class="related-events-section py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="h3 mb-2">you might also like</h2>
            <p class="text-body-secondary">discover more events in <?= $category ?></p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($relatedEvents as $related): ?>
                <?php
                $relatedTitle = htmlspecialchars((string) $related['title']);
                $relatedDescription = htmlspecialchars((string) ($related['description'] ?? 'No description available'));
                $relatedLocation = htmlspecialchars((string) ($related['location'] ?? 'Location not set'));
                $relatedDate = !empty($related['event_date']) ? date('M d, Y', strtotime((string) $related['event_date'])) : 'Date not set';
                $relatedImage = (string) ($related['image'] ?? '');
                $relatedImagePath = $relatedImage !== '' ? 'uploads/' . rawurlencode($relatedImage) : 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=400&q=80';
                ?>
                <div class="col-md-6">
                    <div class="card related-event-card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="row g-0">
                            <div class="col-md-5">
                                <img src="<?= $relatedImagePath ?>" class="img-fluid h-100 object-fit-cover w-100" alt="<?= $relatedTitle ?>">
                            </div>
                            <div class="col-md-7">
                                <div class="card-body p-3 d-flex flex-column h-100">
                                    <span class="badge rounded-pill text-bg-info mb-2 align-self-start"><?= htmlspecialchars($related['category']) ?></span>
                                    <h3 class="h5 card-title mb-2"><?= $relatedTitle ?></h3>
                                    <p class="text-body-secondary small mb-2"><?= $relatedLocation ?></p>
                                    <p class="text-body-secondary small mb-3"><?= mb_strimwidth($relatedDescription, 0, 100, '...') ?></p>
                                    <div class="mt-auto">
                                        <small class="text-body-secondary"><?= $relatedDate ?></small>
                                    </div>
                                    <a href="event.php?id=<?= (int) $related['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill mt-2">view event</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php echo renderShareModal("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", $title); ?>


<?php renderSiteFooter(); ?>