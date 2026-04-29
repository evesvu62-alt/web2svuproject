<?php
declare(strict_types=1);

require_once __DIR__ . '/../session_bootstrap.php';
startAppSession();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

$event = null;
$title = '';
$description = '';
$category = '';
$location = '';
$event_date = '';
$image = '';
$error = '';
$success = '';

// Get event ID from URL parameter
$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($eventId > 0) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute(['id' => $eventId]);
        $event = $stmt->fetch();
        
        if ($event) {
            $title = (string) $event['title'];
            $description = (string) $event['description'];
            $category = (string) ($event['category'] ?? '');
            $location = (string) ($event['location'] ?? '');
            $event_date = (string) ($event['event_date'] ?? '');
            $image = (string) ($event['image'] ?? '');
        }
    } catch (PDOException $e) {
        $error = 'Failed to fetch event: ' . $e->getMessage();
    }
}

if (!$event) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    
    // Handle file upload
    $newImage = $image;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $fileName = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if file is an image
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $newImage = $fileName;
            } else {
                $error = 'Failed to upload image file.';
            }
        } else {
            $error = 'Invalid file type. Only JPG, PNG, GIF, and WebP files are allowed.';
        }
    }
    
    if (empty($title)) {
        $error = 'Title is required';
    } elseif (empty($description)) {
        $error = 'Description is required';
    } else {
        try {
            $stmt = $pdo->prepare('UPDATE events SET title = :title, description = :description, category = :category, location = :location, event_date = :event_date, image = :image WHERE id = :id');
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'location' => $location,
                'event_date' => $event_date,
                'image' => $newImage,
                'id' => $eventId
            ]);
            
            $success = 'Event updated successfully!';
            // Update the event variable
            $event['title'] = $title;
            $event['description'] = $description;
            $event['category'] = $category;
            $event['location'] = $location;
            $event['event_date'] = $event_date;
            $event['image'] = $newImage;
            $image = $newImage;
        } catch (PDOException $e) {
            $error = 'Failed to update event: ' . $e->getMessage();
        }
    }
}

function renderEventFormHeader(string $pageTitle = 'Edit Event'): void
{
    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$pageTitle}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-body">
HTML;
}

function renderEventFormFooter(): void
{
    echo <<<HTML
<script src="../assets/js/main.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
HTML;
}

renderEventFormHeader('Edit Event - UNI Events');
?>

<div class="dashboard-container">
    <!-- Fixed Sidebar -->
    <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="sidebar-header p-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <button class="btn sidebar-toggle-btn sidebar-toggle-inside d-md-none" type="button" data-sidebar-toggle aria-label="Close sidebar">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="sidebar-brand">
                    <span class="brand-mark">UN</span>
                </div>
                <div>
                    <h5 class="sidebar-title mb-0">Admin Panel</h5>
                    <small class="sidebar-subtitle text-body-secondary">City Events</small>
                </div>
            </div>
        </div>
        
        <nav class="sidebar-nav p-3">
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_event.php">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add Event
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../events.php" target="_blank">
                        <i class="bi bi-eye me-2"></i>
                        View Site
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer p-3 mt-auto">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-info small">
                    <div class="fw-semibold">Admin</div>
                    <div class="text-body-secondary"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'User') ?></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="dashboard-main">
        <div class="dashboard-header p-4 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn sidebar-toggle-btn d-md-none" type="button" data-sidebar-toggle aria-controls="dashboardSidebar" aria-expanded="false" aria-label="Open sidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                    <h1 class="h3 mb-1">Edit Event</h1>
                    <p class="text-body-secondary mb-0">Update event information</p>
                    </div>
                </div>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="dashboard-content p-4">
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

            <div class="event-form-container">
                <form method="POST" enctype="multipart/form-data" class="event-form">
                    <div class="form-grid">
                        <!-- Row 1: Title and Category -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="title" class="form-label fw-semibold">Title *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       placeholder="Enter event title"
                                       value="<?= htmlspecialchars($title) ?>"
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="category" class="form-label fw-semibold">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Select category</option>
                                    <option value="Culture" <?= $category === 'Culture' ? 'selected' : '' ?>>Culture</option>
                                    <option value="Tech" <?= $category === 'Tech' ? 'selected' : '' ?>>Tech</option>
                                    <option value="Sports" <?= $category === 'Sports' ? 'selected' : '' ?>>Sports</option>
                                    <option value="Music" <?= $category === 'Music' ? 'selected' : '' ?>>Music</option>
                                    <option value="General" <?= $category === 'General' ? 'selected' : '' ?>>General</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Date and Location -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="event_date" class="form-label fw-semibold">Event Date</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="event_date" 
                                       name="event_date"
                                       value="<?= htmlspecialchars($event_date) ?>">
                            </div>
                            <div class="form-group">
                                <label for="location" class="form-label fw-semibold">Location</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="location" 
                                       name="location" 
                                       placeholder="Enter event location"
                                       value="<?= htmlspecialchars($location) ?>">
                            </div>
                        </div>

                        <!-- Row 3: Description (full width) -->
                        <div class="form-row-full">
                            <div class="form-group">
                                <label for="description" class="form-label fw-semibold">Description *</label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="6" 
                                          placeholder="Enter event description"
                                          required><?= htmlspecialchars($description) ?></textarea>
                            </div>
                        </div>

                        <!-- Row 4: Image Upload (full width) -->
                        <div class="form-row-full">
                            <div class="form-group">
                                <label for="image" class="form-label fw-semibold">Event Image</label>
                                <input type="file" 
                                       class="form-control" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*">
                                <small class="text-body-secondary">Optional: Upload a new image to replace the current one (JPG, PNG, GIF, WebP)</small>
                                
                                <?php if ($image !== ''): ?>
                                    <div class="mt-2">
                                        <small class="text-body-secondary">Current image:</small>
                                        <div class="current-image-preview mt-1">
                                            <img src="../uploads/<?= rawurlencode($image) ?>" 
                                                 alt="Current event image" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 200px; max-height: 150px; object-fit: cover;"
                                                 onerror="this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=200&q=150'">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            Update Event
                        </button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php renderEventFormFooter(); ?>