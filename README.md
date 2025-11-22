# Health and Safety Inventory Management System

Production-ready inventory management system for tracking products, stock levels, and transactions.

## Quick Start

### 1. Upload Files
Upload all files to your web server.

### 2. Create Database
```sql
CREATE DATABASE handsDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'handsuser'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON handsDB.* TO 'handsuser'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Import Schema
The `database/schema.sql` file contains the latest schema updates, including the fixes identified in `database_fix.pdf`.
```bash
mysql -u handsuser -p handsDB < database/schema.sql
```

### 4. Configure Environment
```bash
cp .env.example .env
nano .env
```

Update these values:
- `APP_URL` - Your application URL
- `DB_PASS` - Your database password
- `SECURE_AUTH_KEY` - Generate unique key
- `SECURE_AUTH_SALT` - Generate unique salt

### 5. Set Permissions
```bash
chmod 755 logs/
chmod 666 logs/error.log
chmod 755 public/uploads/
```

### 6. Access Application
Navigate to your application URL and login.

## Requirements

- PHP 8.1+
- MySQL 5.7+
- Apache/Nginx
- PHP Extensions: pdo_mysql, mbstring, json

## Features

- Product Management
- Stock Tracking (In/Out/Transfer)
- Multi-location Inventory
- Low Stock Alerts
- Activity Logging
- User Authentication
- LDAP/AD Integration (optional)

## Support

For issues or questions, contact: admin@sbastola.com
