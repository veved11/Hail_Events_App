<?php
include 'db.php';

// Create users table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','organizer','admin') DEFAULT 'user',
    phone VARCHAR(30),
    avatar VARCHAR(255),
    preferences JSON,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create categories table
$sql_categories = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE,
    color VARCHAR(7) DEFAULT '#00A878',
    icon VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_categories) === TRUE) {
    echo "Categories table created successfully<br>";
} else {
    echo "Error creating categories table: " . $conn->error . "<br>";
}

// Create venues table
$sql_venues = "CREATE TABLE IF NOT EXISTS venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(500),
    lat DECIMAL(10,7),
    lng DECIMAL(10,7),
    contact_info VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_venues) === TRUE) {
    echo "Venues table created successfully<br>";
} else {
    echo "Error creating venues table: " . $conn->error . "<br>";
}

// Create events table
$sql_events = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    short_description VARCHAR(300),
    description LONGTEXT,
    category_id INT,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME,
    venue_id INT,
    price DECIMAL(8,2) DEFAULT 0.00,
    capacity INT,
    registration_type ENUM('free','registration','paid') DEFAULT 'free',
    status ENUM('draft','pending','published','cancelled') DEFAULT 'draft',
    images JSON,
    views INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (venue_id) REFERENCES venues(id)
)";

if ($conn->query($sql_events) === TRUE) {
    echo "Events table created successfully<br>";
} else {
    echo "Error creating events table: " . $conn->error . "<br>";
}

// Create registrations table
$sql_registrations = "CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    ticket_type VARCHAR(100),
    quantity INT DEFAULT 1,
    amount_paid DECIMAL(8,2),
    status ENUM('confirmed','pending','cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql_registrations) === TRUE) {
    echo "Registrations table created successfully<br>";
} else {
    echo "Error creating registrations table: " . $conn->error . "<br>";
}

// Create notifications table
$sql_notifications = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50),
    payload JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql_notifications) === TRUE) {
    echo "Notifications table created successfully<br>";
} else {
    echo "Error creating notifications table: " . $conn->error . "<br>";
}

// Create reviews table
$sql_reviews = "CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql_reviews) === TRUE) {
    echo "Reviews table created successfully<br>";
} else {
    echo "Error creating reviews table: " . $conn->error . "<br>";
}

// Create saved_events table
$sql_saved_events = "CREATE TABLE IF NOT EXISTS saved_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (user_id, event_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
)";

if ($conn->query($sql_saved_events) === TRUE) {
    echo "Saved events table created successfully<br>";
} else {
    echo "Error creating saved events table: " . $conn->error . "<br>";
}

echo "<br>All tables created successfully!";
?>
