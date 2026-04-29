<?php
declare(strict_types=1);

require_once __DIR__ . '/../session_bootstrap.php';
startAppSession();

// Check if admin is logged in
if (
    !isset($_SESSION["admin_logged_in"]) ||
    $_SESSION["admin_logged_in"] !== true
) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../db.php";

$events = [];
$error = "";
$success = "";

// handle query parameters from redirects
if (isset($_GET["success"])) {
    $success = htmlspecialchars($_GET["success"]);
}
if (isset($_GET["error"])) {
    $error = htmlspecialchars($_GET["error"]);
}

try {
    $stmt = $pdo->query(
        "SELECT id, title, category, location, event_date, image FROM events ORDER BY event_date DESC, id DESC",
    );
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Failed to fetch events: " . $e->getMessage();
}

function renderDashboardHeader(string $pageTitle = "Admin Dashboard"): void
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

function renderDashboardFooter(): void
{
    echo <<<HTML
    <script src="../assets/js/main.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>
    HTML;
}

renderDashboardHeader("Admin Dashboard - UNI Events");
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
                    <span class="brand-mark">UE</span>
                </div>
                <div>
                    <h5 class="sidebar-title mb-0">Admin Panel</h5>
                    <small class="sidebar-subtitle text-body-secondary">University Events</small>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav p-3">
            <ul class="nav nav-pills flex-column gap-1">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
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

        <!-- Sidebar Footer -->
        <div class="sidebar-footer p-3 mt-auto">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-info small">
                    <div class="fw-semibold">Admin</div>
                    <div class="text-body-secondary"><?= htmlspecialchars(
                        $_SESSION["admin_username"] ?? "User",
                    ) ?></div>
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
                    <h1 class="h3 mb-1">Event management</h1>
                    <p class="text-body-secondary mb-0">Manage and organize your UNI events</p>
                    </div>
                </div>
                <a href="add_event.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Add New Event
                </a>
            </div>
        </div>

        <div class="dashboard-content p-4">
            <?php if ($error !== ""): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success !== ""): ?>
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($events)): ?>
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-3">
                        <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="mb-2">No Events Found</h4>
                    <p class="text-body-secondary mb-4">Start by adding your first event to the system</p>
                    <a href="add_event.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>
                        Add Your First Event
                    </a>
                </div>
            <?php else: ?>
                <div class="events-table-container">
                    <div class="table-responsive">
                        <table class="table events-table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Category</th>
                                    <th>Location</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                    <?php
                                    $id = (int) $event["id"];
                                    $title = htmlspecialchars(
                                        (string) $event["title"],
                                    );
                                    $category = htmlspecialchars(
                                        (string) ($event["category"] ??
                                            "General"),
                                    );
                                    $location = htmlspecialchars(
                                        (string) ($event["location"] ??
                                            "Not set"),
                                    );
                                    $eventDate = !empty($event["event_date"])
                                        ? date(
                                            "M d, Y",
                                            strtotime(
                                                (string) $event["event_date"],
                                            ),
                                        )
                                        : "Not set";
                                    $imageName =
                                        (string) ($event["image"] ?? "");
                                    ?>
                                    <tr class="event-row">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if ($imageName !== ""): ?>
                                                    <img src="../uploads/<?= rawurlencode(
                                                        $imageName,
                                                    ) ?>"
                                                         class="event-thumbnail"
                                                         alt="<?= $title ?>"
                                                         onerror="this.src='https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=60&q=60'">
                                                <?php else: ?>
                                                    <div class="event-thumbnail-placeholder">
                                                        <i class="bi bi-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-semibold"><?= $title ?></div>
                                                    <small class="text-body-secondary">ID: #<?= $id ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="category-badge"><?= $category ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="bi bi-geo-alt text-body-secondary"></i>
                                                <span><?= $location ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="bi bi-calendar text-body-secondary"></i>
                                                <span><?= $eventDate ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons d-flex justify-content-center gap-1">
                                                <a href="edit_event.php?id=<?= $id ?>"
                                                   class="btn btn-sm btn-outline-primary action-btn"
                                                   title="Edit Event">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="delete_event.php?id=<?= $id ?>"
                                                   class="btn btn-sm btn-outline-danger action-btn"
                                                   title="Delete Event"
                                                   onclick="return confirm('Are you sure you want to delete this event?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-info mt-3">
                    <small class="text-body-secondary">
                        <i class="bi bi-info-circle me-1"></i>
                        showing <?= count($events) ?> event<?= count(
     $events,
 ) !== 1
     ? "s"
     : "" ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php renderDashboardFooter(); ?>