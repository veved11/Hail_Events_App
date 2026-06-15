<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$event_id = (int)($_GET['event_id'] ?? 0);
$user_id = getCurrentUser();

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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_type = sanitize($_POST['ticket_type'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 1);
    $amount_paid = $event['price'] * $quantity;

    if (empty($ticket_type) || $quantity < 1) {
        $error = 'Please select a valid ticket type and quantity.';
    } else {
        // Check if user already registered
        $check_sql = "SELECT id FROM registrations WHERE event_id = ? AND user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $event_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'You have already registered for this event.';
        } else {
            // Insert registration
            $reg_sql = "INSERT INTO registrations (event_id, user_id, ticket_type, quantity, amount_paid, status) 
                       VALUES (?, ?, ?, ?, ?, 'confirmed')";
            $reg_stmt = $conn->prepare($reg_sql);
            $reg_stmt->bind_param("iisid", $event_id, $user_id, $ticket_type, $quantity, $amount_paid);

            if ($reg_stmt->execute()) {
                $success = 'Registration successful! Check your dashboard for confirmation.';
            } else {
                $error = 'Error during registration. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - <?php echo htmlspecialchars($event['title']); ?> - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="flex-between">
                <div class="logo">🎉 Hail Events</div>
                <div class="nav-right">
                    <a href="event-details.php?id=<?php echo $event_id; ?>" class="btn btn-small btn-secondary">Back to Event</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Booking Section -->
    <section class="container mt-3" style="max-width: 700px;">
        <h1>Complete Your Registration</h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Booking Form -->
            <div class="card">
                <h2>Booking Details</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <a href="dashboard.php" class="btn btn-primary" style="width: 100%;">Go to Dashboard</a>
                <?php else: ?>
                    <form method="POST" onsubmit="return HailEvents.validateForm('bookingForm');" id="bookingForm">
                        <div class="form-group">
                            <label for="ticket_type">Ticket Type *</label>
                            <select id="ticket_type" name="ticket_type" required>
                                <option value="">Select ticket type...</option>
                                <option value="General">General Admission</option>
                                <option value="VIP">VIP</option>
                                <option value="Student">Student</option>
                                <option value="Family">Family Pack</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Number of Tickets *</label>
                            <input type="number" id="quantity" name="quantity" min="1" max="10" value="1" required>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" required>
                                I agree to the terms and conditions
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Complete Registration</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Order Summary -->
            <div class="card">
                <h2>Order Summary</h2>
                
                <div style="margin-bottom: 20px;">
                    <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                    <p>
                        <strong>Date:</strong> <?php echo formatDate($event['start_datetime']); ?><br>
                        <strong>Time:</strong> <?php echo date('h:i A', strtotime($event['start_datetime'])); ?><br>
                        <strong>Location:</strong> <?php echo getVenueName($event['venue_id'], $conn); ?>
                    </p>
                </div>

                <hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">

                <div style="margin-bottom: 15px;">
                    <div class="flex-between">
                        <span>Ticket Price:</span>
                        <strong><?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'FREE'; ?></strong>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <div class="flex-between">
                        <span>Quantity:</span>
                        <strong id="qty-display">1</strong>
                    </div>
                </div>

                <hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">

                <div class="flex-between" style="font-size: 18px;">
                    <strong>Total:</strong>
                    <strong id="total-display"><?php echo $event['price'] > 0 ? '$' . number_format($event['price'], 2) : 'FREE'; ?></strong>
                </div>

                <?php if ($event['price'] > 0): ?>
                    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 15px;">
                        💳 Payment will be processed securely
                    </p>
                <?php endif; ?>
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
        // Update total when quantity changes
        document.getElementById('quantity')?.addEventListener('change', function() {
            const quantity = parseInt(this.value) || 1;
            const price = <?php echo $event['price']; ?>;
            const total = price * quantity;
            
            document.getElementById('qty-display').textContent = quantity;
            document.getElementById('total-display').textContent = 
                price > 0 ? '$' + (total).toFixed(2) : 'FREE';
        });
    </script>
</body>
</html>
