<?php
include '../includes/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$event_id = (int)($data['event_id'] ?? 0);
$user_id = getCurrentUser();

if ($event_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid event']);
    exit;
}

$sql = "DELETE FROM saved_events WHERE user_id = ? AND event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Event removed']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error removing event']);
}
?>
