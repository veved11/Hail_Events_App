<?php
include '../includes/db.php';
include '../includes/functions.php';

header('Content-Type: application/json');

$query = sanitize($_GET['q'] ?? '');
$category = (int)($_GET['category'] ?? 0);
$min_price = (float)($_GET['min_price'] ?? 0);
$max_price = (float)($_GET['max_price'] ?? 10000);
$date_from = sanitize($_GET['date_from'] ?? '');
$date_to = sanitize($_GET['date_to'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'date');
$page = (int)($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Build query
$sql = "SELECT * FROM events WHERE status = 'published'";
$params = [];
$types = "";

// Search by title or description
if (!empty($query)) {
    $search_query = "%$query%";
    $sql .= " AND (title LIKE ? OR description LIKE ? OR short_description LIKE ?)";
    $params = array_merge($params, [$search_query, $search_query, $search_query]);
    $types .= "sss";
}

// Filter by category
if ($category > 0) {
    $sql .= " AND category_id = ?";
    $params[] = $category;
    $types .= "i";
}

// Filter by price
$sql .= " AND price BETWEEN ? AND ?";
$params[] = $min_price;
$params[] = $max_price;
$types .= "dd";

// Filter by date
if (!empty($date_from)) {
    $sql .= " AND start_datetime >= ?";
    $params[] = $date_from . " 00:00:00";
    $types .= "s";
}

if (!empty($date_to)) {
    $sql .= " AND start_datetime <= ?";
    $params[] = $date_to . " 23:59:59";
    $types .= "s";
}

// Sorting
switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;
    case 'popular':
        $sql .= " ORDER BY views DESC";
        break;
    case 'date':
    default:
        $sql .= " ORDER BY start_datetime ASC";
        break;
}

// Get total count
$count_sql = str_replace("SELECT *", "SELECT COUNT(*) as count", explode(" ORDER BY", $sql)[0]);
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total = $count_result->fetch_assoc()['count'];

// Get paginated results
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($event = $result->fetch_assoc()) {
    $events[] = $event;
}

echo json_encode([
    'success' => true,
    'total' => $total,
    'page' => $page,
    'limit' => $limit,
    'pages' => ceil($total / $limit),
    'events' => $events
]);
?>
