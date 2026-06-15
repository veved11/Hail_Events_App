<?php
include 'includes/db.php';
include 'includes/functions.php';

$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('m'));

// Validate month and year
if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

// Get first day of month and number of days
$first_day = mktime(0, 0, 0, $month, 1, $year);
$num_days = date('t', $first_day);
$start_weekday = date('w', $first_day);

// Get events for this month
$sql = "SELECT id, title, start_datetime, category_id FROM events 
        WHERE status = 'published' 
        AND MONTH(start_datetime) = ? 
        AND YEAR(start_datetime) = ? 
        ORDER BY start_datetime";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$events_result = $stmt->get_result();

$events_by_day = [];
while ($event = $events_result->fetch_assoc()) {
    $day = (int)date('d', strtotime($event['start_datetime']));
    if (!isset($events_by_day[$day])) {
        $events_by_day[$day] = [];
    }
    $events_by_day[$day][] = $event;
}

$month_name = date('F', $first_day);
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 700;
            padding: 10px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
        }

        .calendar-day {
            min-height: 100px;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 10px;
            background: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .calendar-day:hover {
            background: var(--background);
            border-color: var(--primary);
        }

        .calendar-day.other-month {
            background: #f5f5f5;
            color: #999;
        }

        .calendar-day-number {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .calendar-event {
            font-size: 11px;
            padding: 2px 4px;
            margin: 2px 0;
            border-radius: 3px;
            background: var(--secondary);
            color: white;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .event-count {
            font-size: 12px;
            color: var(--primary);
            font-weight: 600;
        }

        .view-switcher {
            display: flex;
            gap: 10px;
        }

        .view-switcher button {
            padding: 8px 16px;
            border: 2px solid var(--primary);
            background: white;
            color: var(--primary);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .view-switcher button.active {
            background: var(--primary);
            color: white;
        }
    </style>
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
                                $dashboardLink = "user-dashboard.php";
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

    <!-- Calendar Section -->
    <section class="container mt-3">
        <h1>Event Calendar</h1>

        <div class="card">
            <!-- Calendar Toolbar -->
            <div class="calendar-header">
                <div>
                    <a href="calendar.php?year=<?php echo $prev_year; ?>&month=<?php echo $prev_month; ?>" class="btn btn-secondary">← Previous</a>
                </div>
                
                <div style="text-align: center;">
                    <h2><?php echo $month_name . ' ' . $year; ?></h2>
                </div>

                <div>
                    <a href="calendar.php?year=<?php echo $next_year; ?>&month=<?php echo $next_month; ?>" class="btn btn-secondary">Next →</a>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
                <!-- Day Headers -->
                <div class="calendar-day-header">Sun</div>
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>

                <!-- Empty cells for days before month starts -->
                <?php for ($i = 0; $i < $start_weekday; $i++): ?>
                    <div class="calendar-day other-month"></div>
                <?php endfor; ?>

                <!-- Days of month -->
                <?php for ($day = 1; $day <= $num_days; $day++): ?>
                    <div class="calendar-day">
                        <div class="calendar-day-number"><?php echo $day; ?></div>
                        
                        <?php if (isset($events_by_day[$day])): ?>
                            <div class="event-count"><?php echo count($events_by_day[$day]); ?> event(s)</div>
                            <?php foreach (array_slice($events_by_day[$day], 0, 2) as $event): ?>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" class="calendar-event" title="<?php echo htmlspecialchars($event['title']); ?>">
                                    <?php echo htmlspecialchars(substr($event['title'], 0, 15)); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>

                <!-- Empty cells for days after month ends -->
                <?php 
                $total_cells = $start_weekday + $num_days;
                $remaining_cells = (7 - ($total_cells % 7)) % 7;
                for ($i = 0; $i < $remaining_cells; $i++): 
                ?>
                    <div class="calendar-day other-month"></div>
                <?php endfor; ?>
            </div>

            <!-- Legend -->
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <p><strong>Legend:</strong> Click on any day to see all events for that day.</p>
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
</body>
</html>
