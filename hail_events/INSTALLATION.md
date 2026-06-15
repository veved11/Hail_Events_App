# Hail Events - Installation Guide

## System Requirements

- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher
- **Web Server:** Apache with mod_rewrite enabled, or Nginx
- **Extensions:** mysqli, json, PDO (optional)

## Step-by-Step Installation

### 1. Download and Extract Files

```bash
# Extract the project files to your web server directory
# For Apache: /var/www/html/hail_events
# For local development: C:\xampp\htdocs\hail_events
```

### 2. Create Database

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Create a new database named `hail_events`
3. Select the database
4. Go to SQL tab
5. Copy and paste the SQL commands from `includes/create_tables.php`
6. Execute the SQL

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE hail_events;
USE hail_events;
# Then run the SQL commands from includes/create_tables.php
```

**Option C: Automatic (Recommended)**
1. Open your browser
2. Navigate to: `http://localhost/hail_events/includes/create_tables.php`
3. The tables will be created automatically

### 3. Configure Database Connection

Edit `includes/db.php` and update the credentials:

```php
$servername = "localhost";  // Your MySQL server
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password
$dbname = "hail_events";    // Database name
```

### 4. Set File Permissions

For Linux/Mac:
```bash
chmod 755 /path/to/hail_events
chmod 755 /path/to/hail_events/images
chmod 644 /path/to/hail_events/*.php
```

### 5. Configure Web Server

**For Apache:**
- Ensure `mod_rewrite` is enabled
- `.htaccess` file is included in the project
- Restart Apache

**For Nginx:**
Add this to your server block:
```nginx
location / {
    try_files $uri $uri/ $uri.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### 6. Access the Application

Open your browser and navigate to:
```
http://localhost/hail_events/
```

## Initial Setup

### Create Admin User

1. Register a new account through the website
2. Access your database via phpMyAdmin
3. Update the user role to 'admin':
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
   ```

### Add Sample Data

**Add Categories:**
```sql
INSERT INTO categories (name, slug, color) VALUES 
('Music', 'music', '#FF6B6B'),
('Sports', 'sports', '#4ECDC4'),
('Kids', 'kids', '#FFE66D'),
('Lectures', 'lectures', '#95E1D3');
```

**Add Venues:**
```sql
INSERT INTO venues (name, address, lat, lng) VALUES 
('Hail Convention Center', 'Hail, Saudi Arabia', 27.5166, 41.7208),
('Al-Qassim Sports Hall', 'Hail, Saudi Arabia', 27.5200, 41.7250);
```

## Troubleshooting

### Database Connection Error
- Check database credentials in `includes/db.php`
- Ensure MySQL server is running
- Verify database exists: `SHOW DATABASES;`

### Permission Denied Error
- Check file permissions
- Ensure web server has read/write access to directories

### White Screen of Death
- Check PHP error logs
- Enable error reporting in `includes/db.php`
- Verify PHP version compatibility

### 404 Errors
- Ensure `.htaccess` is present
- Check Apache `mod_rewrite` is enabled
- Verify file names are correct

## Security Recommendations

1. **Change Default Credentials:**
   - Update database password
   - Use strong passwords for admin accounts

2. **Enable HTTPS:**
   - Install SSL certificate
   - Update all URLs to use HTTPS

3. **Backup Database:**
   - Create regular backups
   - Store backups securely

4. **Update Regularly:**
   - Keep PHP updated
   - Update MySQL regularly
   - Monitor for security patches

5. **Restrict Admin Access:**
   - Limit admin panel access by IP
   - Use strong authentication

## Performance Optimization

1. **Database Indexing:**
   ```sql
   CREATE INDEX idx_event_status ON events(status);
   CREATE INDEX idx_event_date ON events(start_datetime);
   CREATE INDEX idx_user_email ON users(email);
   ```

2. **Caching:**
   - Enable PHP opcode caching
   - Use browser caching

3. **Image Optimization:**
   - Compress images before upload
   - Use appropriate image formats

## Support

For issues or questions:
- Check the README.md file
- Review error logs
- Contact: info@hailevents.com

## Next Steps

1. Customize the design to match your brand
2. Add your logo and images
3. Configure email notifications
4. Set up payment processing (optional)
5. Promote your platform
