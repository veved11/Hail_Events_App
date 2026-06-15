<?php
include 'includes/db.php';
include 'includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message_text = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = 'Please fill in all fields.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        // In a real application, you would send an email here
        $message = 'Thank you for your message! We will get back to you soon.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hail Events</title>
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

    <!-- Contact Section -->
    <section class="container mt-3" style="max-width: 800px;">
        <h1>Contact Us</h1>
        <p>Have a question or suggestion? We'd love to hear from you!</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
            <!-- Contact Form -->
            <div class="card">
                <h2>Send us a Message</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST" onsubmit="return HailEvents.validateForm('contactForm');" id="contactForm">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="card">
                <h2>Contact Information</h2>
                
                <div style="margin-bottom: 20px;">
                    <h4>📍 Address</h4>
                    <p>Hail, Saudi Arabia</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <h4>📞 Phone</h4>
                    <p>+966 (0) 123 456 789</p>
                </div>

                <div style="margin-bottom: 20px;">
                    <h4>📧 Email</h4>
                    <p><a href="mailto:info@hailevents.com">info@hailevents.com</a></p>
                </div>

                <div style="margin-bottom: 20px;">
                    <h4>🕐 Business Hours</h4>
                    <p>Saturday - Thursday: 9:00 AM - 6:00 PM<br>
                    Friday: Closed</p>
                </div>

                <div>
                    <h4>Follow Us</h4>
                    <div class="flex" style="gap: 15px;">
                        <a href="#" class="btn btn-small btn-secondary">Facebook</a>
                        <a href="#" class="btn btn-small btn-secondary">Twitter</a>
                        <a href="#" class="btn btn-small btn-secondary">Instagram</a>
                    </div>
                </div>
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
