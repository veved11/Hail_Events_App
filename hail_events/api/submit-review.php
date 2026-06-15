<?php
include '../includes/db.php';
include '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = (int)($_POST['event_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = sanitize($_POST['comment'] ?? '');
    $user_id = getCurrentUser();

    if ($event_id === 0 || $rating < 1 || $rating > 5) {
        redirect('../event-details.php?id=' . $event_id);
    }

    // Check if user already reviewed
    $check_sql = "SELECT id FROM reviews WHERE event_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $event_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        $sql = "INSERT INTO reviews (event_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $event_id, $user_id, $rating, $comment);
        $stmt->execute();
    }

    redirect('../event-details.php?id=' . $event_id);
}
?>
