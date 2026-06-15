<?php
include 'includes/db.php';
include 'includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$action = sanitize($_GET['action'] ?? '');
$user_id = (int)($_GET['user_id'] ?? 0);
$error = '';
$success = '';

// Handle actions
if ($action === 'delete' && $user_id > 0) {
    $sql = "DELETE FROM users WHERE id = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $current_user = getCurrentUser();
    $stmt->bind_param("ii", $user_id, $current_user);
    if ($stmt->execute()) {
        $success = 'User deleted successfully.';
    } else {
        $error = 'Error deleting user.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $role = sanitize($_POST['role'] ?? 'user');

    if ($user_id > 0 && getCurrentUser() !== $user_id) {
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $role, $user_id);
        if ($stmt->execute()) {
            $success = 'User role updated successfully.';
        } else {
            $error = 'Error updating user role.';
        }
    }
}

// Get all users
$page = (int)($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

$total_sql = "SELECT COUNT(*) as count FROM users";
$total = $conn->query($total_sql)->fetch_assoc()['count'];
$total_pages = ceil($total / $limit);

$sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$users = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .user-table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-table th {
            background: var(--primary);
            color: white;
            padding: 12px;
            text-align: left;
        }

        .user-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .user-table tr:hover {
            background: var(--background);
        }

        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .role-user {
            background: #E3F2FD;
            color: #1565C0;
        }

        .role-organizer {
            background: #FFF3E0;
            color: #E65100;
        }

        .role-admin {
            background: #F3E5F5;
            color: #6A1B9A;
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

    <!-- Manage Users -->
    <section class="container mt-3">
        <h1>Manage Users</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Total Users: <?php echo $total; ?></h2>

            <table class="user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" onchange="this.form.submit()" style="padding: 5px; border-radius: 5px;">
                                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="organizer" <?php echo $user['role'] === 'organizer' ? 'selected' : ''; ?>>Organizer</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </form>
                                <?php if (getCurrentUser() !== $user['id']): ?>
                                    <a href="?action=delete&user_id=<?php echo $user['id']; ?>" class="btn btn-small btn-secondary" 
                                       onclick="return confirm('Are you sure?');">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 20px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
