<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isLoggedIn() || !isOrganizer()) {
    redirect('login.php');
}

$user_id = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $short_description = sanitize($_POST['short_description'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $start_datetime = sanitize($_POST['start_datetime'] ?? '');
    $end_datetime = sanitize($_POST['end_datetime'] ?? '');
    $venue_id = (int)($_POST['venue_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $capacity = (int)($_POST['capacity'] ?? 0);
    $registration_type = sanitize($_POST['registration_type'] ?? 'free');

    if (empty($title) || empty($description) || empty($start_datetime)) {
        $error = 'Please fill in all required fields.';
    } else {
        $slug = generateSlug($title);
        $images = json_encode([]);
        $status = 'draft';

        $sql = "INSERT INTO events (organizer_id, title, slug, short_description, description, category_id, 
                start_datetime, end_datetime, venue_id, price, capacity, registration_type, images, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssiissidss", $user_id, $title, $slug, $short_description, $description, 
                         $category_id, $start_datetime, $end_datetime, $venue_id, $price, $capacity, 
                         $registration_type, $images, $status);

        if ($stmt->execute()) {
            $success = 'Event created successfully! You can now edit it and add more details.';
        } else {
            $error = 'Error creating event. Please try again.';
        }
    }
}

// Get categories and venues
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$venues = $conn->query("SELECT * FROM venues ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="flex-between">
                <div class="logo">🎉 Hail Events</div>
                <div class="nav-right">
                    <a href="organizer-dashboard.php" class="btn btn-small btn-secondary">Back to Dashboard</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Create Event Form -->
    <section class="container mt-3" style="max-width: 800px;">
        <h1>Create New Event</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="organizer-dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        <?php else: ?>
            <div class="card">
                <form method="POST" onsubmit="return HailEvents.validateForm('createEventForm');" id="createEventForm">
                    <div class="form-group">
                        <label for="title">Event Title *</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="short_description">Short Description (200 chars) *</label>
                        <textarea id="short_description" name="short_description" maxlength="200" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description">Full Description *</label>
                        <textarea id="description" name="description" required style="min-height: 200px;"></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select category...</option>
                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="venue_id">Venue</label>
                            <select id="venue_id" name="venue_id">
                                <option value="">Select venue...</option>
                                <?php while ($venue = $venues->fetch_assoc()): ?>
                                    <option value="<?php echo $venue['id']; ?>"><?php echo htmlspecialchars($venue['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="start_datetime">Start Date & Time *</label>
                            <input type="datetime-local" id="start_datetime" name="start_datetime" required>
                        </div>

                        <div class="form-group">
                            <label for="end_datetime">End Date & Time</label>
                            <input type="datetime-local" id="end_datetime" name="end_datetime">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label for="price">Price (0 for free) *</label>
                            <input type="number" id="price" name="price" min="0" step="0.01" value="0" required>
                        </div>

                        <div class="form-group">
                            <label for="capacity">Capacity (0 for unlimited)</label>
                            <input type="number" id="capacity" name="capacity" min="0" value="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="registration_type">Registration Type *</label>
                        <select id="registration_type" name="registration_type" required>
                            <option value="free">Free</option>
                            <option value="registration">Registration Required</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Create Event</button>
                </form>
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
