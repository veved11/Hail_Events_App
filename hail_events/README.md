# Hail Events Platform

A comprehensive full-stack event management platform built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

### For Users
- Browse and search events
- View detailed event information
- Register for events
- Save favorite events
- View event calendar
- Leave reviews and ratings
- Manage registrations from dashboard
- Receive notifications

### For Event Organizers
- Create and manage events
- Track registrations
- View event statistics
- Manage event details and images
- Organizer dashboard

### For Administrators
- Approve/reject pending events
- Manage users and roles
- Manage categories and venues
- View platform statistics
- Monitor registrations
- Generate reports

## Project Structure

```
hail_events/
├── index.php                 # Home page
├── register.php             # User registration
├── login.php                # User login
├── logout.php               # User logout
├── events.php               # Events list and search
├── event-details.php        # Event details page
├── calendar.php             # Calendar view
├── categories.php           # Categories page
├── about.php                # About page
├── contact.php              # Contact page
├── booking.php              # Event booking/registration
├── dashboard.php            # User dashboard
├── organizer-dashboard.php  # Organizer dashboard
├── create-event.php         # Create new event
├── admin-dashboard.php      # Admin dashboard
│
├── includes/
│   ├── db.php              # Database connection
│   ├── create_tables.php   # Create database tables
│   └── functions.php       # Helper functions
│
├── css/
│   └── style.css           # Main stylesheet
│
├── js/
│   └── main.js             # JavaScript functions
│
└── images/                 # Image assets
```

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

### Setup Steps

1. **Create the database:**
   - Open your MySQL client
   - Run the SQL commands from `includes/create_tables.php`
   - Or visit `includes/create_tables.php` in your browser to auto-create tables

2. **Configure database connection:**
   - Edit `includes/db.php`
   - Update database credentials:
     ```php
     $servername = "localhost";
     $username = "root";
     $password = "";
     $dbname = "hail_events";
     ```

3. **Place files on web server:**
   - Copy all files to your web server directory
   - Ensure proper permissions for file uploads

4. **Access the application:**
   - Open `http://localhost/hail_events/` in your browser

## Usage

### User Registration
1. Click "Register" button
2. Fill in required information
3. Choose account type (User or Organizer)
4. Click "Create Account"

### Browsing Events
1. Go to "Events" page
2. Use filters to search by category, date, or price
3. Click on event card to view details
4. Register for events you're interested in

### Creating Events (Organizers)
1. Login as organizer
2. Go to "Organizer Dashboard"
3. Click "Create New Event"
4. Fill in event details
5. Submit for approval

### Admin Functions
1. Login as admin
2. Access "Admin Dashboard"
3. Approve/reject pending events
4. Manage users, categories, and venues
5. View statistics and reports

## Database Schema

### Users Table
- id, name, email, password_hash, role, phone, avatar, preferences, created_at, updated_at

### Events Table
- id, organizer_id, title, slug, short_description, description, category_id, start_datetime, end_datetime, venue_id, price, capacity, registration_type, images, views, status, created_at, updated_at

### Categories Table
- id, name, slug, color, icon, created_at

### Venues Table
- id, name, address, lat, lng, contact_info, created_at

### Registrations Table
- id, event_id, user_id, ticket_type, quantity, amount_paid, status, created_at

### Notifications Table
- id, user_id, type, payload, is_read, created_at

### Reviews Table
- id, event_id, user_id, rating, comment, created_at

### Saved Events Table
- id, user_id, event_id, created_at

## Color Scheme

- **Primary:** #0B6E4F (Deep Green)
- **Secondary:** #00A878 (Medium Green)
- **Accent:** #FF9F1C (Orange)
- **Background:** #F7F9FA (Light Gray)
- **Text Main:** #0F1724 (Dark)
- **Text Secondary:** #556070 (Gray)
- **Error:** #E63946 (Red)

## API Endpoints

### Public
- `GET /` - Home page
- `GET /events` - Events list
- `GET /event-details.php?id={id}` - Event details
- `GET /calendar.php` - Calendar view
- `GET /categories.php` - Categories page

### Authentication
- `POST /register.php` - User registration
- `POST /login.php` - User login
- `GET /logout.php` - User logout

### User
- `GET /dashboard.php` - User dashboard
- `POST /booking.php` - Register for event

### Organizer
- `GET /organizer-dashboard.php` - Organizer dashboard
- `GET /create-event.php` - Create event form
- `POST /create-event.php` - Submit new event

### Admin
- `GET /admin-dashboard.php` - Admin dashboard
- `POST /api/approve-event.php` - Approve event
- `POST /api/reject-event.php` - Reject event

## Security Features

- Password hashing with bcrypt
- Input sanitization
- Session management
- Role-based access control
- SQL injection prevention with prepared statements

## Future Enhancements

- Payment gateway integration (Stripe, PayPal)
- Email notifications
- Advanced analytics
- Event recommendations
- Social media integration
- Mobile app
- Real-time notifications
- Event attendance tracking

## Support

For issues or questions, please contact: info@hailevents.com

## License

© 2024 Hail Events. All rights reserved.
