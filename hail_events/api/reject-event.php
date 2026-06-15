<?php
include '../includes/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$event_id = (int)($data['event_id'] ?? 0);

if ($event_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid event']);
    exit;
}

$sql = "UPDATE events SET status = 'cancelled' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Event rejected']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error rejecting event']);
}
?>
