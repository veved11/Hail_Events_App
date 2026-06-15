<?php
include 'includes/db.php';
include 'includes/functions.php';

$event_id = (int)($_GET['id'] ?? 0);

if ($event_id === 0) {
    redirect('events.php');
}

// Get event details
$sql = "SELECT * FROM events WHERE id = ? AND status = 'published'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('events.php');
}

$event = $result->fetch_assoc();

// Update views
$sql_update = "UPDATE events SET views = views + 1 WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("i", $event_id);
$stmt_update->execute();

// Get reviews
$sql_reviews = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.event_id = ? ORDER BY r.created_at DESC";
$stmt_reviews = $conn->prepare($sql_reviews);
$stmt_reviews->bind_param("i", $event_id);
$stmt_reviews->execute();
$reviews_result = $stmt_reviews->get_result();

// Get related events
$sql_related = "SELECT * FROM events WHERE category_id = ? AND id != ? AND status = 'published' LIMIT 3";
$stmt_related = $conn->prepare($sql_related);
$stmt_related->bind_param("ii", $event['category_id'], $event_id);
$stmt_related->execute();
$related_result = $stmt_related->get_result();

$images = json_decode($event['images'], true) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Hail Events</title>
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
                        <a href="dashboard.php" class="btn btn-small btn-primary">Dashboard</a>
                        <a href="logout.php" class="btn btn-small btn-secondary">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-small btn-secondary">Login</a>
                        <a href="register.php" class="btn btn-small btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Event Details -->
    <section class="container mt-3">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Left Column - Images & Description -->
            <div>
                <!-- Main Image -->
                <div style="margin-bottom: 20px;">
                    <img src="<?php echo htmlspecialchars($images[0] ?? 'images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style="width: 100%; border-radius: 12px; max-height: 400px; object-fit: cover;">
                </div>

                <!-- Image Gallery -->
                <?php if (count($images) > 1): ?>
                    <div style="display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto;">
                        <?php foreach ($images as $img): ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Gallery" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; cursor: pointer;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Description -->
                <div class="card">
                    <h2>About This Event</h2>
                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                </div>

                <!-- Reviews Section -->
                <div class="card mt-3">
                    <h3>Reviews (<?php echo $reviews_result->num_rows; ?>)</h3>
                    
                    <?php if (isLoggedIn()): ?>
                        <form method="POST" action="api/submit-review.php" class="form-group">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                            
                            <label>Rating</label>
                            <select name="rating" required>
                                <option value="">Select rating...</option>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                <option value="4">⭐⭐⭐⭐ Good</option>
                                <option value="3">⭐⭐⭐ Average</option>
                                <option value="2">⭐⭐ Poor</option>
                                <option value="1">⭐ Very Poor</option>
                            </select>

                            <label>Comment</label>
                            <textarea name="comment" placeholder="Share your experience..."></textarea>

                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    <?php else: ?>
                        <p><a href="login.php">Login</a> to leave a review.</p>
                    <?php endif; ?>

                    <div style="margin-top: 20px;">
                        <?php
                        while ($review = $reviews_result->fetch_assoc()) {
                            echo '<div class="card" style="margin-bottom: 15px;">';
                            echo '<div class="flex-between">';
                            echo '<strong>' . htmlspecialchars($review['name']) . '</strong>';
                            echo '<span>' . str_repeat('⭐', $review['rating']) . '</span>';
                            echo '</div>';
                            echo '<small>' . formatDate($review['created_at']) . '</small>';
                            echo '<p>' . htmlspecialchars($review['comment']) . '</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Event Info & Booking -->
            <div>
                <div class="card">
                    <h2><?php echo htmlspecialchars($event['title']); ?></h2>
                    
                    <span class="event-category"><?php echo getCategoryName($event['category_id'], $conn); ?></span>

                    <div class="event-meta" style="margin: 20px 0;">
                        <div class="event-meta-item">
                            <span>📅 Date:</span>
                            <strong><?php echo formatDate($event['start_datetime']); ?></strong>
                        </div>
                        <div class="event-meta-item">
                            <span>🕒 Time:</span>
                            <strong><?php echo date('h:i A', strtotime($event['start_datetime'])); ?></strong>
                        </div>
                        <div class="event-meta-item">
                            <span>📍 Location:</span>
                            <strong><?php echo getVenueName($event['venue_id'], $conn); ?></strong>
                        </div>
                        <div class="event-meta-item">
                            <span>👥 Capacity:</span>
                            <strong><?php echo $event['capacity'] ?? 'Unlimited'; ?></strong>
                        </div>
                        <div class="event-meta-item">
                            <span>👁️ Views:</span>
                            <strong><?php echo $event['views']; ?></strong>
                        </div>
                    </div>

                    <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

                    <!-- Price & Registration -->
                    <div style="margin: 20px 0;">
                        <h3 class="event-price">
                            <?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'FREE'; ?>
                        </h3>
                        <p style="color: var(--text-secondary);">
                            Registration Type: <strong><?php echo ucfirst($event['registration_type']); ?></strong>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <?php if (isLoggedIn()): ?>
                        <a href="booking.php?event_id=<?php echo $event_id; ?>" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">Register Now</a>
                        <button class="btn btn-secondary" style="width: 100%;" onclick="HailEvents.addToFavorites(<?php echo $event_id; ?>)">❤️ Add to Favorites</button>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary" style="width: 100%; margin-bottom: 10px;">Login to Register</a>
                        <a href="register.php" class="btn btn-secondary" style="width: 100%;">Create Account</a>
                    <?php endif; ?>

                    <!-- Organizer Info -->
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                        <h4>Event Organizer</h4>
                        <p><?php echo getUserName($event['organizer_id'], $conn); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Events -->
    <?php if ($related_result->num_rows > 0): ?>
        <section class="container mt-3">
            <h2>Related Events</h2>
            <div class="grid-3">
                <?php
                while ($related = $related_result->fetch_assoc()) {
                    $rel_images = json_decode($related['images'], true);
                    $rel_image = $rel_images[0] ?? 'images/placeholder.jpg';
                    ?>
                    <div class="event-card">
                        <img src="<?php echo htmlspecialchars($rel_image); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" class="event-image">
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($related['title']); ?></h3>
                            <div class="event-footer">
                                <span class="event-price">
                                    <?php echo $related['price'] > 0 ? '$' . number_format($related['price'], 2) : 'FREE'; ?>
                                </span>
                                <a href="event-details.php?id=<?php echo $related['id']; ?>" class="btn btn-small btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>
    <?php endif; ?>

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
