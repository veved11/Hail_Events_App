<?php
include 'includes/db.php';
include 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = getCurrentUser();
$user_role = getUserRole();

// Only allow organizers
if ($user_role !== 'organizer') {
    redirect('index.php');
}

// Get organizer info
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$organizer = $stmt->get_result()->fetch_assoc();

// Get organizer's events
$sql = "SELECT e.*, c.name as category, COUNT(r.id) as registrations FROM events e
        LEFT JOIN categories c ON e.category_id = c.id
        LEFT JOIN registrations r ON e.id = r.event_id
        WHERE e.organizer_id = ?
        GROUP BY e.id
        ORDER BY e.start_datetime DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events = $stmt->get_result();

// Get total stats
$sql = "SELECT COUNT(*) as total_events FROM events WHERE organizer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_events = $stmt->get_result()->fetch_assoc()['total_events'];

$sql = "SELECT COUNT(*) as total_registrations FROM registrations r
        JOIN events e ON r.event_id = e.id
        WHERE e.organizer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_registrations = $stmt->get_result()->fetch_assoc()['total_registrations'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>🎉 Hail Events</h3>
                <p>Organizer Dashboard</p>
            </div>

            <nav class="sidebar-nav">
                <li><a href="organizer-dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="create-event.php">➕ Create Event</a></li>
                <li><a href="events.php">🎪 Browse Events</a></li>
                <li><a href="edit-profile.php">👤 Edit Profile</a></li>
               
            </nav>

            <div class="sidebar-footer">
                <p>Welcome, <?php echo htmlspecialchars($organizer['name']); ?>!</p>
                <a href="logout.php">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <h1>Organizer Dashboard</h1>
                <p>Manage your events and registrations</p>
            </div>

            <!-- Tabs Navigation -->
            <div class="tabs">
                <button class="tab-button active" data-tab="overview">Overview</button>
                <button class="tab-button" data-tab="events">My Events</button>
                <button class="tab-button" data-tab="registrations">Registrations</button>
            </div>

            <!-- Overview Tab -->
            <div id="overview" class="tab-content active">
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_events; ?></div>
                        <div class="stat-label">Total Events</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_registrations; ?></div>
                        <div class="stat-label">Total Registrations</div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <h3>Quick Actions</h3>
                    <a href="create-event.php" class="btn btn-primary">Create New Event</a>
                </div>
            </div>

            <!-- Events Tab -->
            <div id="events" class="tab-content">
                <div class="dashboard-card">
                    <h3>My Events</h3>
                    <?php if ($events->num_rows > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Event Title</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Registrations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = $events->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                                        <td><?php echo htmlspecialchars($event['category']); ?></td>
                                        <td><?php echo formatDate($event['start_datetime']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $event['status']; ?>">
                                                <?php echo ucfirst($event['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $event['registrations']; ?></td>
                                        <td class="actions">
                                            <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn-view">View</a>
                                            <a href="edit-event.php?id=<?php echo $event['id']; ?>" class="btn-edit">Edit</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>You haven't created any events yet. <a href="create-event.php">Create your first event</a></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Registrations Tab -->
            <div id="registrations" class="tab-content">
                <div class="dashboard-card">
                    <h3>Event Registrations</h3>
                    <?php 
                    $sql = "SELECT r.*, e.title as event_title, u.name as user_name FROM registrations r
                            JOIN events e ON r.event_id = e.id
                            JOIN users u ON r.user_id = u.id
                            WHERE e.organizer_id = ?
                            ORDER BY r.created_at DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $registrations = $stmt->get_result();
                    ?>
                    
                    <?php if ($registrations->num_rows > 0): ?>
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Event</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($reg = $registrations->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reg['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($reg['event_title']); ?></td>
                                        <td><?php echo $reg['quantity']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $reg['status']; ?>">
                                                <?php echo ucfirst($reg['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($reg['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No registrations yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="js/dashboard.js"></script>
</body>
</html>
