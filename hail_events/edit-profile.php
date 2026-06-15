<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = getCurrentUser();
$error = '';
$success = '';

// Get user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email)) {
        $error = 'Name and email are required.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email is already used by another user
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $email, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'Email already in use.';
        } else {
            // Update basic info
            $update_sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssi", $name, $email, $phone, $user_id);

            if ($update_stmt->execute()) {
                // Update password if provided
                if (!empty($new_password)) {
                    if (!verifyPassword($current_password, $user['password_hash'])) {
                        $error = 'Current password is incorrect.';
                    } elseif ($new_password !== $confirm_password) {
                        $error = 'New passwords do not match.';
                    } elseif (strlen($new_password) < 6) {
                        $error = 'New password must be at least 6 characters.';
                    } else {
                        $password_hash = hashPassword($new_password);
                        $pwd_sql = "UPDATE users SET password_hash = ? WHERE id = ?";
                        $pwd_stmt = $conn->prepare($pwd_sql);
                        $pwd_stmt->bind_param("si", $password_hash, $user_id);
                        $pwd_stmt->execute();
                        $success = 'Profile and password updated successfully!';
                    }
                } else {
                    $success = 'Profile updated successfully!';
                }

                // Refresh user data
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $_SESSION['user_name'] = $name;
            } else {
                $error = 'Error updating profile. Please try again.';
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
    <title>Edit Profile - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="flex-between">
                <div class="logo">🎉 Hail Events</div>
                <div class="nav-right">
                    <a href="dashboard.php" class="btn btn-small btn-secondary">Back to Dashboard</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Edit Profile Form -->
    <section class="container mt-3" style="max-width: 600px;">
        <h1>Edit Profile</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" onsubmit="return HailEvents.validateForm('editProfileForm');" id="editProfileForm">
                <h2>Personal Information</h2>

                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>

                <div class="form-group">
                    <label>Account Type</label>
                    <input type="text" value="<?php echo ucfirst($user['role']); ?>" disabled>
                    <small>Contact admin to change account type</small>
                </div>

                <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

                <h2>Change Password</h2>
                <p style="color: var(--text-secondary); margin-bottom: 20px;">Leave blank if you don't want to change your password</p>

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password">
                    <small>Minimum 6 characters</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
            </form>
        </div>

        <!-- Account Statistics -->
        <div class="card mt-3">
            <h2>Account Information</h2>
            <p><strong>Member Since:</strong> <?php echo formatDate($user['created_at']); ?></p>
            <p><strong>Last Updated:</strong> <?php echo formatDate($user['updated_at']); ?></p>
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
