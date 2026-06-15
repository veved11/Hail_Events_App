<?php
include 'includes/db.php';
include 'includes/functions.php';

$sql = "SELECT c.*, COUNT(e.id) as event_count FROM categories c 
        LEFT JOIN events e ON c.id = e.category_id AND e.status = 'published'
        GROUP BY c.id
        ORDER BY c.name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Hail Events</title>
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

    <!-- Categories Section -->
    <section class="container category-container mt-3">
        <h1>Event Categories</h1>
        <p>Browse events by category to find what interests you.</p>

        <div class="grid-3" style="margin-top: 30px;">
            <?php
            while ($category = $result->fetch_assoc()) {
                $color = $category['color'] ?? '#00A878';
                ?>
                <div class="card" style="text-align: center; cursor: pointer; transition: var(--transition);" 
                     onclick="window.location.href='events.php?category=<?php echo $category['id']; ?>'">
                    <div class = "icon-circle" style= "background: <?php echo htmlspecialchars($color); ?>">
                        📌
                    </div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p style="font-size: 18px; font-weight: 700; color: var(--primary);">
                        <?php echo $category['event_count']; ?> Events
                    </p>
                    <a href="events.php?category=<?php echo $category['id']; ?>" class="btn btn-small btn-primary">View Events</a>
                </div>
                <?php
            }
            ?>
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
