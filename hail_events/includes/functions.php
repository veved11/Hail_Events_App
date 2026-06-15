<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Generate slug from title
function generateSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user
function getCurrentUser() {
    return $_SESSION['user_id'] ?? null;
}

// Get user role
function getUserRole() {
    return $_SESSION['user_role'] ?? 'user';
}

// Check if user is admin
function isAdmin() {
    return getUserRole() === 'admin';
}

// Check if user is organizer
function isOrganizer() {
    return getUserRole() === 'organizer';
}

// Redirect to page
function redirect($page) {
    header("Location: " . $page);
    exit();
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

// Get time difference
function getTimeDifference($datetime) {
    $now = new DateTime();
    $date = new DateTime($datetime);
    $interval = $now->diff($date);
    
    if ($interval->days > 0) {
        return $interval->days . ' days ago';
    } elseif ($interval->h > 0) {
        return $interval->h . ' hours ago';
    } elseif ($interval->i > 0) {
        return $interval->i . ' minutes ago';
    } else {
        return 'Just now';
    }
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Get category color
function getCategoryColor($categoryId, $conn) {
    $sql = "SELECT color FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['color'] ?? '#00A878';
}

// Get category name
function getCategoryName($categoryId, $conn) {
    $sql = "SELECT name FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['name'] ?? 'Unknown';
}

// Get user name by ID
function getUserName($userId, $conn) {
    $sql = "SELECT name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['name'] ?? 'Unknown User';
}

// Get venue name by ID
function getVenueName($venueId, $conn) {
    $sql = "SELECT name FROM venues WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $venueId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['name'] ?? 'Unknown Venue';
}

// Count events by category
function countEventsByCategory($categoryId, $conn) {
    $sql = "SELECT COUNT(*) as count FROM events WHERE category_id = ? AND status = 'published'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}

// Get upcoming events
function getUpcomingEvents($conn, $limit = 6) {
    $sql = "SELECT * FROM events WHERE status = 'published' AND start_datetime > NOW() ORDER BY start_datetime ASC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Search events
function searchEvents($conn, $keyword = '', $category = '', $date = '', $location = '') {
    $sql = "SELECT * FROM events WHERE status = 'published'";
    $params = [];
    $types = '';

    if (!empty($keyword)) {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $keyword = "%$keyword%";
        $params[] = $keyword;
        $params[] = $keyword;
        $types .= 'ss';
    }

    if (!empty($category)) {
        $sql .= " AND category_id = ?";
        $params[] = $category;
        $types .= 'i';
    }

    if (!empty($date)) {
        $sql .= " AND DATE(start_datetime) = ?";
        $params[] = $date;
        $types .= 's';
    }

    $sql .= " ORDER BY start_datetime ASC";

    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}

?>
