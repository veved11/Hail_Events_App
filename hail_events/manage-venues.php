<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$error = '';
$success = '';

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $venue_id = (int)($_GET['id'] ?? 0);
    if ($venue_id > 0) {
        $sql = "DELETE FROM venues WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $venue_id);
        if ($stmt->execute()) {
            $success = 'Venue deleted successfully.';
        } else {
            $error = 'Error deleting venue.';
        }
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $venue_id = (int)($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $lat = (float)($_POST['lat'] ?? 0);
    $lng = (float)($_POST['lng'] ?? 0);
    $contact_info = sanitize($_POST['contact_info'] ?? '');

    if (empty($name) || empty($address)) {
        $error = 'Name and address are required.';
    } else {
        if ($venue_id > 0) {
            // Update
            $sql = "UPDATE venues SET name = ?, address = ?, lat = ?, lng = ?, contact_info = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddssi", $name, $address, $lat, $lng, $contact_info, $venue_id);
        } else {
            // Insert
            $sql = "INSERT INTO venues (name, address, lat, lng, contact_info) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdds", $name, $address, $lat, $lng, $contact_info);
        }

        if ($stmt->execute()) {
            $success = $venue_id > 0 ? 'Venue updated successfully.' : 'Venue added successfully.';
        } else {
            $error = 'Error saving venue.';
        }
    }
}

// Get all venues
$venues = $conn->query("SELECT * FROM venues ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Venues - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .venue-table {
            width: 100%;
            border-collapse: collapse;
        }

        .venue-table th {
            background: var(--primary);
            color: white;
            padding: 12px;
            text-align: left;
        }

        .venue-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .venue-table tr:hover {
            background: var(--background);
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

    <!-- Manage Venues -->
    <section class="container mt-3">
        <h1>Manage Venues</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Add Venue Form -->
        <div class="card" style="margin-bottom: 30px;">
            <h2>Add New Venue</h2>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label for="name">Venue Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <input type="text" id="address" name="address" required>
                    </div>

                    <div class="form-group">
                        <label for="lat">Latitude</label>
                        <input type="number" id="lat" name="lat" step="0.0001" placeholder="27.5166">
                    </div>

                    <div class="form-group">
                        <label for="lng">Longitude</label>
                        <input type="number" id="lng" name="lng" step="0.0001" placeholder="41.7208">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="contact_info">Contact Information</label>
                        <input type="text" id="contact_info" name="contact_info" placeholder="+966 123 456 789">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Add Venue</button>
            </form>
        </div>

        <!-- Venues List -->
        <div class="card">
            <h2>Existing Venues (<?php echo $venues->num_rows; ?>)</h2>

            <table class="venue-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Coordinates</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($venue = $venues->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($venue['name']); ?></td>
                            <td><?php echo htmlspecialchars($venue['address']); ?></td>
                            <td>
                                <?php if ($venue['lat'] && $venue['lng']): ?>
                                    <?php echo number_format($venue['lat'], 4); ?>, <?php echo number_format($venue['lng'], 4); ?>
                                <?php else: ?>
                                    <span style="color: #999;">Not set</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($venue['contact_info']); ?></td>
                            <td>
                                <a href="?action=delete&id=<?php echo $venue['id']; ?>" class="btn btn-small btn-secondary"
                                   onclick="return confirm('Delete this venue?');">Delete</a>
                            </td>
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
