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
    $cat_id = (int)($_GET['id'] ?? 0);
    if ($cat_id > 0) {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        if ($stmt->execute()) {
            $success = 'Category deleted successfully.';
        } else {
            $error = 'Error deleting category.';
        }
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat_id = (int)($_POST['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $color = sanitize($_POST['color'] ?? '#00A878');

    if (empty($name)) {
        $error = 'Category name is required.';
    } else {
        if ($cat_id > 0) {
            // Update
            $sql = "UPDATE categories SET name = ?, color = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $name, $color, $cat_id);
        } else {
            // Insert
            $slug = generateSlug($name);
            $sql = "INSERT INTO categories (name, slug, color) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $name, $slug, $color);
        }

        if ($stmt->execute()) {
            $success = $cat_id > 0 ? 'Category updated successfully.' : 'Category added successfully.';
        } else {
            $error = 'Error saving category.';
        }
    }
}

// Get all categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Hail Events</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .category-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 20px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .category-card {
            background: white;
            border: 2px solid #eee;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .category-color {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 15px;
        }

        .category-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .category-actions a,
        .category-actions button {
            flex: 1;
            padding: 8px;
            font-size: 12px;
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

    <!-- Manage Categories -->
    <section class="container mt-3">
        <h1>Manage Categories</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Add Category Form -->
        <div class="card" style="margin-bottom: 30px;">
            <h2>Add New Category</h2>
            <form method="POST">
                <div class="category-form">
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="text" name="name" placeholder="Category Name" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <input type="color" name="color" value="#00A878">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>

        <!-- Categories List -->
        <div class="card">
            <h2>Existing Categories (<?php echo $categories->num_rows; ?>)</h2>

            <div class="category-grid">
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <div class="category-card">
                        <div class="category-color" style="background: <?php echo htmlspecialchars($cat['color']); ?>;"></div>
                        <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                        <p style="font-size: 12px; color: var(--text-secondary);">
                            <?php echo countEventsByCategory($cat['id'], $conn); ?> events
                        </p>
                        <div class="category-actions">
                            <a href="?action=delete&id=<?php echo $cat['id']; ?>" class="btn btn-small btn-secondary"
                               onclick="return confirm('Delete this category?');">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
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
