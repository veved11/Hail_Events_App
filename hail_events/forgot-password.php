<?php
include 'includes/db.php';
include 'includes/functions.php';

$error = '';
$success = '';
$step = 1; // Step 1: Enter email, Step 2: Enter new password

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['step']) && $_POST['step'] == 1) {
        $email = sanitize($_POST['email'] ?? '');

        if (empty($email)) {
            $error = 'Please enter your email address.';
        } elseif (!isValidEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Check if email exists
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = 'Email not found in our system.';
            } else {
                // In a real application, you would send a reset link via email
                // For now, we'll just show a message
                $success = 'Password reset instructions have been sent to your email. (In production, this would be sent via email)';
                $step = 2;
            }
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == 2) {
        $email = sanitize($_POST['email'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($new_password) || empty($confirm_password)) {
            $error = 'Please enter and confirm your new password.';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } else {
            $password_hash = hashPassword($new_password);
            $sql = "UPDATE users SET password_hash = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $password_hash, $email);

            if ($stmt->execute()) {
                $success = 'Password has been reset successfully! You can now <a href="login.php">login</a> with your new password.';
            } else {
                $error = 'Error resetting password. Please try again.';
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
    <title>Forgot Password - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="flex-between">
                <div class="logo">🎉 Hail Events</div>
                <div class="nav-right">
                    <a href="index.php" class="btn btn-small btn-secondary">Back to Home</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Forgot Password Form -->
    <section class="container" style="max-width: 500px; margin: 40px auto;">
        <div class="card">
            <h2>Reset Your Password</h2>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if ($step == 1 && empty($success)): ?>
                <p>Enter your email address and we'll send you instructions to reset your password.</p>

                <form method="POST" onsubmit="return HailEvents.validateForm('forgotForm');" id="forgotForm">
                    <input type="hidden" name="step" value="1">

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Send Reset Instructions</button>
                </form>
            <?php elseif ($step == 2 && empty($success)): ?>
                <p>Enter your new password below.</p>

                <form method="POST" onsubmit="return HailEvents.validateForm('resetForm');" id="resetForm">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <small>Minimum 6 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Reset Password</button>
                </form>
            <?php endif; ?>

            <p style="text-align: center; margin-top: 20px;">
                Remember your password? <a href="login.php">Login here</a>
            </p>
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
