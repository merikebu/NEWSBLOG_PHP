
# PHP Blog Platform

A complete blog platform built with pure PHP, MySQL, and Tailwind CSS.

## Features

- User registration and authentication
- Role-based access (Admin/User)
- CRUD operations for blog posts
- Admin approval system for posts and testimonials
- Comment system
- Image upload support
- Search functionality
- Pagination
- Dark mode toggle
- Responsive design with Tailwind CSS

## Setup Instructions

1. **Database Setup**
   - Create a MySQL database
   - Import the `database_schema.sql` file
   - Update database credentials in `config.php`

2. **File Structure**
   ```
   /blog-platform/
   ├── config.php          # Database config and helper functions
   ├── auth.php            # Authentication handling
   ├── posts.php           # Post management class
   ├── index.php           # Homepage with post listing
   ├── admin.php           # Admin dashboard
   ├── login.php           # Login form
   ├── register.php        # Registration form
   ├── create-post.php     # Post creation form
   ├── post.php            # Single post view
   ├── uploads/            # Directory for uploaded images
   └── README.md          # This file
   ```

3. **Permissions**
   - Make sure the `uploads/` directory is writable
   - Set appropriate file permissions for security

4. **Default Admin Account**
   - Username: `admin`
   - Email: `admin@blog.com`
   - Password: `admin123`

## Key Classes and Functions

- `Auth`: Handles user registration, login, logout
- `Posts`: Manages post CRUD operations and approval system
- Helper functions in `config.php` for authentication checks
- PDO for all database interactions

## Security Features

- Password hashing with PHP's password_hash()
- SQL injection prevention using prepared statements
- Input sanitization
- Session management
- File upload validation

## Usage

1. Users can register and create posts
2. Posts are pending approval by default
3. Admins can approve/reject posts from the dashboard
4. Approved posts are visible to all visitors
5. Anyone can comment on approved posts
6. Search and pagination for better navigation

This is a reference implementation that you can deploy on any PHP/MySQL hosting environment.
# NEWSBLOG_PHP
