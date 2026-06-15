<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$pending_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'pending'")->fetch_assoc()['count'];
$total_registrations = $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count'];

// Get pending events
$pending = $conn->query("SELECT * FROM events WHERE status = 'pending' ORDER BY created_at DESC");

// Get recent registrations
$registrations = $conn->query("SELECT r.*, e.title, u.name FROM registrations r 
                               JOIN events e ON r.event_id = e.id 
                               JOIN users u ON r.user_id = u.id 
                               ORDER BY r.created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .stat-card h3 {
            color: white;
            font-size: 32px;
            margin: 10px 0;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            background: var(--primary);
            color: white;
            padding: 12px;
            text-align: left;
        }

        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .admin-table tr:hover {
            background: var(--background);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="flex-between">
                <div class="logo">🎉 Hail Events</div>
                <div class="nav-right">
                    <span>Admin Panel</span>
                    <a href="dashboard.php" class="btn btn-small btn-secondary">User Dashboard</a>
                    <a href="logout.php" class="btn btn-small btn-secondary">Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Admin Dashboard -->
    <section class="container mt-3">
        <h1>Admin Dashboard</h1>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <p>Total Users</p>
                <h3><?php echo $total_users; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Events</p>
                <h3><?php echo $total_events; ?></h3>
            </div>
            <div class="stat-card">
                <p>Pending Approval</p>
                <h3><?php echo $pending_events; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Registrations</p>
                <h3><?php echo $total_registrations; ?></h3>
            </div>
        </div>

        <!-- Pending Events -->
        <div class="card mt-3">
            <h2>Events Pending Approval (<?php echo $pending->num_rows; ?>)</h2>
            
            <?php if ($pending->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Organizer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = $pending->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo getUserName($event['organizer_id'], $conn); ?></td>
                                <td><?php echo formatDate($event['start_datetime']); ?></td>
                                <td><span style="color: var(--warning);">⏳ Pending</span></td>
                                <td class="action-buttons">
                                    <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-small btn-secondary">View</a>
                                    <button class="btn btn-small btn-primary" onclick="approveEvent(<?php echo $event['id']; ?>)">Approve</button>
                                    <button class="btn btn-small btn-secondary" onclick="rejectEvent(<?php echo $event['id']; ?>)">Reject</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No events pending approval.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Registrations -->
        <div class="card mt-3">
            <h2>Recent Registrations</h2>
            
            <?php if ($registrations->num_rows > 0): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Event</th>
                            <th>Ticket Type</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($reg = $registrations->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reg['name']); ?></td>
                                <td><?php echo htmlspecialchars($reg['title']); ?></td>
                                <td><?php echo htmlspecialchars($reg['ticket_type']); ?></td>
                                <td><span style="color: var(--success);">✓ <?php echo ucfirst($reg['status']); ?></span></td>
                                <td><?php echo formatDate($reg['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No registrations yet.</p>
            <?php endif; ?>
        </div>

        <!-- Management Links -->
        <div class="card mt-3">
            <h2>Management</h2>
            <div class="flex" style="gap: 15px;">
                <a href="manage-users.php" class="btn btn-primary">Manage Users</a>
                <a href="manage-categories.php" class="btn btn-primary">Manage Categories</a>
                <a href="manage-venues.php" class="btn btn-primary">Manage Venues</a>
                <a href="reports.php" class="btn btn-primary">View Reports</a>
            </div>
        </div>
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
    <script>
        function approveEvent(eventId) {
            if (confirm('Approve this event?')) {
                fetch('api/approve-event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ event_id: eventId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function rejectEvent(eventId) {
            if (confirm('Reject this event?')) {
                fetch('api/reject-event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ event_id: eventId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>
