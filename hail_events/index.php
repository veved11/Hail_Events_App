<?php
include 'includes/db.php';
include 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hail Events - All Hail Events in One Place</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header & Navigation -->
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>All Hail Events in One Place</h1>
            <p>A live updated calendar for cultural, entertainment, and sports activities — sign up to receive personalized notifications based on your interests.</p>
            
            <div class="hero-controls">
                <input type="text" id="search-input" placeholder="Search for an event, venue, or date…">
                <input type="date" id="filter-date">
                <select id="filter-category">
                    <option value="">All Categories</option>
                    <?php
                    $sql = "SELECT * FROM categories ORDER BY name";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                    }
                    ?>
                </select>
                <button class="btn btn-accent" onclick="HailEvents.filterEvents()">Search</button>
            </div>

            <a href="calendar.php" class="btn btn-large btn-accent" style="margin-top: 20px;">View Calendar</a>
        </div>
    </section>

    <!-- Quick Filters -->
    <section class="container mt-3">
        <div class="filters">
            <div class="filter-group">
                <span class="filter-label">Quick Filters:</span>
                <span class="filter-chip active" onclick="window.location.href='events.php?date=today'">Today</span>
                <span class="filter-chip" onclick="window.location.href='events.php?date=week'">This Week</span>
                <span class="filter-chip" onclick="window.location.href='events.php?date=month'">This Month</span>
                <span class="filter-chip" onclick="window.location.href='events.php?price=free'">Free Events</span>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="container mt-3">
        <h2>Upcoming Events</h2>
        <div class="grid-3">
            <?php
            $result = getUpcomingEvents($conn, 6);
            if ($result->num_rows > 0) {
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
            } else {
                echo '<p>No upcoming events found.</p>';
            }
            ?>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="container mt-3" style="text-align: center; padding: 40px 0;">
        <h2>How It Works</h2>
        <div class="grid-3">
            <div class="card">
                <h3>🔍 Search</h3>
                <p>Browse and search for events by category, date, location, or keyword.</p>
            </div>
            <div class="card">
                <h3>💾 Save & Register</h3>
                <p>Save your favorite events and register for the ones you want to attend.</p>
            </div>
            <div class="card">
                <h3>🎉 Attend</h3>
                <p>Get notifications and enjoy amazing events in Hail.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Hail Events</h3>
                    <p>Your gateway to all cultural, entertainment, and sports activities in Hail.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="calendar.php">Calendar</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="categories.php">Categories</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Company</h3>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="terms.php">Terms & Conditions</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <ul>
                        <li><a href="#">Facebook</a></li>
                        <li><a href="#">Twitter</a></li>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">LinkedIn</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Hail Events. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
