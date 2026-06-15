<?php
include 'includes/db.php';
include 'includes/functions.php';

$keyword = sanitize($_GET['q'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$date = sanitize($_GET['date'] ?? '');
$price = sanitize($_GET['price'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$sql = "SELECT * FROM events WHERE status = 'published'";
$count_sql = "SELECT COUNT(*) as total FROM events WHERE status = 'published'";
$params = [];
$types = '';

if (!empty($keyword)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $count_sql .= " AND (title LIKE ? OR description LIKE ?)";
    $keyword_search = "%$keyword%";
    $params[] = $keyword_search;
    $params[] = $keyword_search;
    $types .= 'ss';
}

if (!empty($category)) {
    $sql .= " AND category_id = ?";
    $count_sql .= " AND category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if (!empty($date)) {
    if ($date === 'today') {
        $sql .= " AND DATE(start_datetime) = CURDATE()";
        $count_sql .= " AND DATE(start_datetime) = CURDATE()";
    } elseif ($date === 'week') {
        $sql .= " AND WEEK(start_datetime) = WEEK(CURDATE())";
        $count_sql .= " AND WEEK(start_datetime) = WEEK(CURDATE())";
    } elseif ($date === 'month') {
        $sql .= " AND MONTH(start_datetime) = MONTH(CURDATE())";
        $count_sql .= " AND MONTH(start_datetime) = MONTH(CURDATE())";
    }
}

if (!empty($price)) {
    if ($price === 'free') {
        $sql .= " AND price = 0";
        $count_sql .= " AND price = 0";
    } elseif ($price === 'paid') {
        $sql .= " AND price > 0";
        $count_sql .= " AND price > 0";
    }
}

$sql .= " ORDER BY start_datetime ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

// Get total count
$count_stmt = $conn->prepare($count_sql);
if (!empty($params) && $types !== 'ii') {
    $count_types = substr($types, 0, -2);
    $count_params = array_slice($params, 0, -2);
    if (!empty($count_params)) {
        $count_stmt->bind_param($count_types, ...$count_params);
    }
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// Get events
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="flex-between">
                <div class="logo">🎉 Hail Events</div>
                
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="calendar.php">Calendar</a></li>
                    <li><a href="events.php">Events</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>

                <div class="nav-right">
                    <?php if (isLoggedIn()): ?>

                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>

                        <?php
                            // تحديد رابط واسم لوحة التحكم حسب الدور
                            $dashboardName = "Dashboard";
                            $dashboardLink = "dashboard.php";  // الرابط الافتراضي

                            if ($_SESSION['user_role'] === 'admin') {
                                $dashboardName = "Admin Panel";
                                $dashboardLink = "admin-dashboard.php";
                            } elseif ($_SESSION['user_role'] === 'organizer') {
                                $dashboardName = "Organizer Dashboard";
                                $dashboardLink = "organizer-dashboard.php";
                            } elseif ($_SESSION['user_role'] === 'user') {
                                $dashboardName = "User Dashboard";
                                $dashboardLink = "dashboard.php";
                            }
                        ?>

                        <a href="<?php echo $dashboardLink; ?>" class="btn btn-small btn-primary">
                            <?php echo $dashboardName; ?>
                        </a>

                        <a href="logout.php" class="btn btn-small btn-secondary">Logout</a>

                    <?php else: ?>

                        <a href="login.php" class="btn btn-small btn-secondary">Login</a>
                        <a href="register.php" class="btn btn-small btn-primary">Register</a>

                    <?php endif; ?>
                </div>

            </nav>
        </div>
    </header>

    <!-- Search & Filters -->
    <section class="container mt-3">
        <h1>Search Events</h1>
        
        <div class="filters">
            <form method="GET" class="filter-group">
                <input type="text" name="q" placeholder="Search events..." value="<?php echo htmlspecialchars($keyword); ?>">
                
                <select name="category">
                    <option value="">All Categories</option>
                    <?php
                    $cat_sql = "SELECT * FROM categories ORDER BY name";
                    $cat_result = $conn->query($cat_sql);
                    while ($cat = $cat_result->fetch_assoc()) {
                        $selected = $category == $cat['id'] ? 'selected' : '';
                        echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['name']) . '</option>';
                    }
                    ?>
                </select>

                <select name="date">
                    <option value="">All Dates</option>
                    <option value="today" <?php echo $date === 'today' ? 'selected' : ''; ?>>Today</option>
                    <option value="week" <?php echo $date === 'week' ? 'selected' : ''; ?>>This Week</option>
                    <option value="month" <?php echo $date === 'month' ? 'selected' : ''; ?>>This Month</option>
                </select>

                <select name="price">
                    <option value="">All Prices</option>
                    <option value="free" <?php echo $price === 'free' ? 'selected' : ''; ?>>Free</option>
                    <option value="paid" <?php echo $price === 'paid' ? 'selected' : ''; ?>>Paid</option>
                </select>

                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </section>

    <!-- Events Grid -->
    <section class="container mt-3">
        <?php if ($result->num_rows > 0): ?>
            <p style="margin-bottom: 20px;">Found <?php echo $total; ?> event(s)</p>
            
            <div class="grid-4">
                <?php
                while ($event = $result->fetch_assoc()) {
                    $images = json_decode($event['images'], true);
                    $image = $images[0] ?? 'images/placeholder.jpg';
                    ?>
                    <div class="event-card">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                        <div class="event-content">
                            <span class="event-category"><?php echo getCategoryName($event['category_id'], $conn); ?></span>
                            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="event-description"><?php echo htmlspecialchars(substr($event['short_description'], 0, 100)); ?>...</p>
                            
                            <div class="event-meta">
                                <div class="event-meta-item">
                                    <span>📅</span>
                                    <span><?php echo formatDate($event['start_datetime']); ?></span>
                                </div>
                                <div class="event-meta-item">
                                    <span>🕒</span>
                                    <span><?php echo date('h:i A', strtotime($event['start_datetime'])); ?></span>
                                </div>
                                <div class="event-meta-item">
                                    <span>📍</span>
                                    <span><?php echo getVenueName($event['venue_id'], $conn); ?></span>
                                </div>
                            </div>

                            <div class="event-footer">
                                <span class="event-price">
                                    <?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'FREE'; ?>
                                </span>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-small btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="events.php?page=<?php echo $page - 1; ?>&q=<?php echo urlencode($keyword); ?>&category=<?php echo $category; ?>&date=<?php echo $date; ?>&price=<?php echo $price; ?>">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="events.php?page=<?php echo $i; ?>&q=<?php echo urlencode($keyword); ?>&category=<?php echo $category; ?>&date=<?php echo $date; ?>&price=<?php echo $price; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="events.php?page=<?php echo $page + 1; ?>&q=<?php echo urlencode($keyword); ?>&category=<?php echo $category; ?>&date=<?php echo $date; ?>&price=<?php echo $price; ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <p>Sorry, no events match your search. Try adjusting your filters.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 Hail Events. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
