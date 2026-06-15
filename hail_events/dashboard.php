<?php
include 'includes/db.php';
include 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = getCurrentUser();
$user_role = getUserRole();

// Only allow regular users
if ($user_role === 'admin' || $user_role === 'organizer') {
    redirect('index.php');
}

// Get user info
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get user's registrations
$sql = "SELECT r.*, e.title, e.start_datetime, e.price, c.name as category FROM registrations r
        JOIN events e ON r.event_id = e.id
        JOIN categories c ON e.category_id = c.id
        WHERE r.user_id = ? ORDER BY e.start_datetime DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$registrations = $stmt->get_result();

// Get user's saved events
$sql = "SELECT e.*, c.name as category FROM saved_events se
        JOIN events e ON se.event_id = e.id
        JOIN categories c ON e.category_id = c.id
        WHERE se.user_id = ? ORDER BY se.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saved_events = $stmt->get_result();

// Get user's reviews
$sql = "SELECT r.*, e.title FROM reviews r
        JOIN events e ON r.event_id = e.id
        WHERE r.user_id = ? ORDER BY r.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>🎉 Hail Events</h3>
                <p>User Dashboard</p>
            </div>

            <nav class="sidebar-nav">
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="events.php">🎪 Browse Events</a></li>
                <li><a href="calendar.php">📅 Calendar</a></li>
                <li><a href="edit-profile.php">👤 Edit Profile</a></li>
                <li><a href="logout.php">🚪 Logout</a></li>
            </nav>

            <div class="sidebar-footer">
                <p>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</p>
                <a href="logout.php">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <h1>My Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="tabs">
                <button class="tab-button active" data-tab="overview">Overview</button>
                <button class="tab-button" data-tab="registrations">My Registrations</button>
                <button class="tab-button" data-tab="saved">Saved Events</button>
                <button class="tab-button" data-tab="reviews">My Reviews</button>
            </div>

            <!-- Overview Tab -->
            <div id="overview" class="tab-content active">
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $registrations->num_rows; ?></div>
                        <div class="stat-label">Registered Events</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $saved_events->num_rows; ?></div>
                        <div class="stat-label">Saved Events</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $reviews->num_rows; ?></div>
                        <div class="stat-label">Reviews Written</div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>Your Profile</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                    <p><strong>Member Since:</strong> <?php echo formatDate($user['created_at']); ?></p>
                    <a href="edit-profile.php" class="btn btn-primary mt-2">Edit Profile</a>
                </div>
            </div>

            <!-- Registrations Tab -->
            <div id="registrations" class="tab-content">
                <div class="dashboard-card">
                    <h3>My Event Registrations</h3>
                    <?php if ($registrations->num_rows > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($reg = $registrations->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reg['title']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['category']); ?></td>
                                        <td><?php echo formatDate($reg['start_datetime']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $reg['status']; ?>">
                                                <?php echo ucfirst($reg['status']); ?>
                                            </span>
                                        </td>
                                        <td class="actions">
                                            <a href="event-details.php?id=<?php echo $reg['event_id']; ?>" class="btn-view">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>You haven't registered for any events yet. <a href="events.php">Browse events</a></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Saved Events Tab -->
            <div id="saved" class="tab-content">
                <div class="dashboard-card">
                    <h3>Saved Events</h3>
                    <?php if ($saved_events->num_rows > 0): ?>
                        <div class="dashboard-grid grid-3">
                            <?php while ($event = $saved_events->fetch_assoc()): ?>
                                <div class="event-card">
                                    <div class="event-image">🎪</div>
                                    <div class="event-content">
                                        <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                                        <h4 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h4>
                                        <p class="event-description"><?php echo htmlspecialchars(substr($event['short_description'], 0, 100)); ?>...</p>
                                        <div class="event-meta">
                                            <span>📅 <?php echo formatDate($event['start_datetime']); ?></span>
                                            <span>💰 $<?php echo number_format($event['price'], 2); ?></span>
                                        </div>
                                        <div class="event-footer">
                                            <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-small btn-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>You haven't saved any events yet. <a href="events.php">Explore events</a></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div id="reviews" class="tab-content">
                <div class="dashboard-card">
                    <h3>My Reviews</h3>
                    <?php if ($reviews->num_rows > 0): ?>
                        <div class="dashboard-grid">
                            <?php while ($review = $reviews->fetch_assoc()): ?>
                                <div class="dashboard-card">
                                    <h4><?php echo htmlspecialchars($review['title']); ?></h4>
                                    <div class="rating">
                                        <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                            ⭐
                                        <?php endfor; ?>
                                    </div>
                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                    <small><?php echo formatDate($review['created_at']); ?></small>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>You haven't written any reviews yet. <a href="events.php">Attend an event and share your experience!</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="js/dashboard.js"></script>
</body>
</html>
