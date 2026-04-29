<?php
declare(strict_types=1);

require_once __DIR__ . '/../session_bootstrap.php';
startAppSession();

// check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../db.php';

// get event ID from URL parameter
$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($eventId > 0) {
    try {
        // first, get the event to check if it exists and get the image filename
        $stmt = $pdo->prepare('SELECT image FROM events WHERE id = :id');
        $stmt->execute(['id' => $eventId]);
        $event = $stmt->fetch();
        
        if ($event) {
            // delete the event from database
            $deleteStmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
            $deleteStmt->execute(['id' => $eventId]);
            
            // if event had an image, try to delete the file
            if (!empty($event['image'])) {
                $imagePath = '../uploads/' . basename($event['image']);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // redirect back to dashboard with success message
            header('Location: dashboard.php?success=Event deleted successfully');
            exit;
        } else {
            // event not found  redirect with error
            header('Location: dashboard.php?error=Event not found');
            exit;
        }
    } catch (PDOException $e) {
        // database error  redirect with error message
        header('Location: dashboard.php?error=Failed to delete event: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // invalid ID  redirect with error
    header('Location: dashboard.php?error=Invalid event ID');
    exit;
}
?>