<?php
include 'includes/db.php';
include 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Hail Events</title>
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

    <!-- About Section -->
    <section class="container mt-3">
        <h1>About Hail Events</h1>

        <div class="card mt-3">
            <h2>Our Mission</h2>
            <p>Hail Events is a local platform connecting residents and visitors with all activities across the region, making it easier to discover and attend events effortlessly.</p>
            <p>We believe that great events bring communities together. Our platform is designed to make event discovery simple, transparent, and enjoyable for everyone.</p>
        </div>

        <div class="card mt-3">
            <h2>What We Offer</h2>
            <ul style="list-style: none;">
                <li style="margin-bottom: 15px;">
                    <strong>📅 Comprehensive Calendar</strong>
                    <p>Browse events by date, category, and location with our interactive calendar view.</p>
                </li>
                <li style="margin-bottom: 15px;">
                    <strong>🔍 Smart Search</strong>
                    <p>Find exactly what you're looking for with advanced filtering and search capabilities.</p>
                </li>
                <li style="margin-bottom: 15px;">
                    <strong>💾 Save & Manage</strong>
                    <p>Save your favorite events and manage your registrations from your personal dashboard.</p>
                </li>
                <li style="margin-bottom: 15px;">
                    <strong>🔔 Notifications</strong>
                    <p>Stay updated with personalized notifications about events that match your interests.</p>
                </li>
                <li style="margin-bottom: 15px;">
                    <strong>⭐ Reviews & Ratings</strong>
                    <p>Read reviews from other attendees and share your experience.</p>
                </li>
            </ul>
        </div>

        <div class="card mt-3">
            <h2>For Event Organizers</h2>
            <p>Are you planning an event? Hail Events makes it easy to reach your audience. Create, promote, and manage your events all in one place.</p>
            <a href="register.php" class="btn btn-primary">Become an Organizer</a>
        </div>

        <div class="card mt-3">
            <h2>Our Team</h2>
            <p>We're a dedicated team passionate about bringing the community together through events. We're constantly working to improve the platform and add new features based on your feedback.</p>
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
