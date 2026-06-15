<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$published_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'published'")->fetch_assoc()['count'];
$total_registrations = $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(amount_paid) as total FROM registrations WHERE status = 'confirmed'")->fetch_assoc()['total'] ?? 0;

// Get events by category
$category_stats = $conn->query("SELECT c.name, COUNT(e.id) as count FROM categories c 
                               LEFT JOIN events e ON c.id = e.category_id AND e.status = 'published'
                               GROUP BY c.id ORDER BY count DESC");

// Get top events by registrations
$top_events = $conn->query("SELECT e.title, COUNT(r.id) as registrations, e.price 
                           FROM events e 
                           LEFT JOIN registrations r ON e.id = r.event_id 
                           WHERE e.status = 'published'
                           GROUP BY e.id 
                           ORDER BY registrations DESC 
                           LIMIT 10");

// Get users by role
$role_stats = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");

// Get recent events
$recent_events = $conn->query("SELECT * FROM events ORDER BY created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Hail Events</title>
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

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .report-table th {
            background: var(--primary);
            color: white;
            padding: 12px;
            text-align: left;
        }

        .report-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .report-table tr:hover {
            background: var(--background);
        }

        .chart-container {
            margin-top: 20px;
        }

        .bar {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .bar-label {
            width: 150px;
            font-weight: 600;
        }

        .bar-fill {
            flex: 1;
            height: 25px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 5px;
            margin: 0 10px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            color: white;
            font-weight: 600;
        }

        .bar-value {
            width: 50px;
            text-align: right;
            font-weight: 600;
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
                    <a href="admin-dashboard.php" class="btn btn-small btn-secondary">Back to Admin</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Reports -->
    <section class="container mt-3">
        <h1>Platform Reports & Analytics</h1>

        <!-- Key Statistics -->
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
                <p>Published Events</p>
                <h3><?php echo $published_events; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Registrations</p>
                <h3><?php echo $total_registrations; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Revenue</p>
                <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
            </div>
        </div>

        <!-- Users by Role -->
        <div class="card mt-3">
            <h2>Users by Role</h2>
            <div class="chart-container">
                <?php while ($role = $role_stats->fetch_assoc()): ?>
                    <div class="bar">
                        <div class="bar-label"><?php echo ucfirst($role['role']); ?></div>
                        <div class="bar-fill" style="width: <?php echo ($role['count'] / $total_users * 100); ?>%;">
                            <?php echo $role['count']; ?>
                        </div>
                        <div class="bar-value"><?php echo round(($role['count'] / $total_users * 100), 1); ?>%</div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Events by Category -->
        <div class="card mt-3">
            <h2>Events by Category</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Number of Events</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cat = $category_stats->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><?php echo $cat['count']; ?></td>
                            <td><?php echo round(($cat['count'] / $published_events * 100), 1); ?>%</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Top Events by Registrations -->
        <div class="card mt-3">
            <h2>Top 10 Events by Registrations</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Registrations</th>
                        <th>Price</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = $top_events->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo $event['registrations']; ?></td>
                            <td><?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'FREE'; ?></td>
                            <td><?php echo $event['price'] > 0 ? '$' . number_format($event['price'] * $event['registrations'], 2) : '-'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Events -->
        <div class="card mt-3">
            <h2>Recent Events</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Organizer</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($event = $recent_events->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo getUserName($event['organizer_id'], $conn); ?></td>
                            <td><?php echo formatDate($event['created_at']); ?></td>
                            <td><span style="color: var(--primary); font-weight: 600;"><?php echo ucfirst($event['status']); ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
</body>
</html>
