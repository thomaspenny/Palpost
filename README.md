# Palpost
A mock social media page I created where you can create a fake profile and check out more images of my portfolio. You can find the webiste hosted here:

http://palpost.atwebpages.com/login.php

You'll need to create a dummy account to enter the website: fake username, fake email and a password you don't use anywhere important (which is encrypted in the DB).

## 🌟 Features

### User Management
- **User Registration & Authentication**: Secure signup and login system
- **Admin Authorization**: Special admin accounts with elevated privileges
- **Profile Management**: Users can update their profiles and profile pictures
- **Session Management**: Secure session handling with CSRF token protection

### Content Management
- **Post Creation**: Users can create text posts with up to 5 media attachments
- **Media Support**: Upload and display images and other media files
- **Post Viewing**: Browse posts in chronological order or sorted by popularity
- **Interactive Content**: Like posts and view engagement statistics

### Social Features
- **Like System**: Users can like/unlike posts
- **Engagement Metrics**: Display like counts and comment counts
- **User Profiles**: View user information and their posts
- **Content Discovery**: Browse posts by newest or most popular

## 📁 Project Structure

```
code/
├── index.php                 # Main homepage with post feed
├── login.php                 # User authentication page
├── user_signup.php           # Regular user registration
├── admin_signup.php          # Admin account creation
├── new_post.php              # Post creation interface
├── posts_display.php         # Post viewing and interaction
├── profile.php               # User profile management
├── settings.php              # User settings and preferences
├── banner.php                # Website header/navigation
├── style.css                 # Custom CSS styling
├── background_fns/           # Backend functionality
│   ├── connection.php        # Database connection
│   ├── functions.php         # Core utility functions
│   ├── post_functions.php    # Post-related functions
│   ├── like_post.php         # Like/unlike functionality
│   ├── like_script.php       # AJAX like handling
│   └── logout.php            # Session termination
├── Images/                   # Site assets (logo, etc.)
├── uploads/                  # User-generated content
│   ├── profiles/             # Profile pictures
│   └── post_content/         # Post media files
```

## 🔧 Technical Requirements

### Server Requirements
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Server**: Apache or Nginx with PHP support
- **Extensions**: MySQLi, GD (for image handling), fileinfo

### Browser Compatibility
- Chrome/Chromium (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## 🚀 Installation & Setup

### 1. Database Configuration
1. Create a MySQL database
2. Update database credentials in `background_fns/connection.php`:
```php
$dbhost = 'your_host';
$dbuser = 'your_username';
$dbpass = 'your_password';
$dbname = 'your_database';
```

### 2. Database Schema
Create the following tables in your MySQL database:

```sql
-- Users table
CREATE TABLE Users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    userName VARCHAR(50) UNIQUE NOT NULL,
    userEmail VARCHAR(100) UNIQUE NOT NULL,
    userPassword VARCHAR(255) NOT NULL,
    userRank TINYINT DEFAULT 0,
    userImagePath VARCHAR(255) DEFAULT 'uploads/profiles/default.png',
    userImageType VARCHAR(50) DEFAULT 'image/png',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Posts table
CREATE TABLE Posts (
    postID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    TextContent TEXT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE
);

-- Likes table
CREATE TABLE Likes (
    likeID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    postID INT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (userID, postID),
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE
);

-- Comments table (if implemented)
CREATE TABLE Comments (
    commentID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    postID INT NOT NULL,
    CommentText TEXT NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES Users(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE
);

-- PostMedia table
CREATE TABLE PostMedia (
    mediaID INT AUTO_INCREMENT PRIMARY KEY,
    postID INT NOT NULL,
    mediaPath VARCHAR(255) NOT NULL,
    mediaType VARCHAR(50) NOT NULL,
    mediaCaption TEXT,
    UploadedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (postID) REFERENCES Posts(postID) ON DELETE CASCADE
);
```

### 3. File Permissions
Set appropriate permissions for upload directories:
```bash
chmod 755 uploads/
chmod 755 uploads/profiles/
chmod 755 uploads/post_content/
```

### 4. Default Assets
1. Place a default profile image at `uploads/profiles/default.png`
2. Add your logo to `Images/logo.png`

## 👤 User Management

### Creating the First Admin Account
**Important**: The admin signup page requires existing admin authorization by default. For initial setup:

1. **Temporarily disable authorization** (see comments in `admin_signup.php`)
2. Create your first admin account
3. **Re-enable authorization** for security

### User Roles
- **Regular Users** (`userRank = 0`): Can create posts, like content, manage their profile
- **Administrators** (`userRank = 1`): Have additional privileges and can create other admin accounts

## 🔐 Security Features

### Authentication & Authorization
- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **CSRF Protection**: Token-based protection against cross-site request forgery
- **Session Security**: Secure session management with regeneration
- **Input Sanitization**: All user input is sanitized and validated
- **SQL Injection Prevention**: Prepared statements used throughout

### File Upload Security
- **File Type Validation**: Restricted file types for uploads
- **File Size Limits**: Maximum file size restrictions
- **Secure Upload Path**: Files stored outside web root when possible

## 📱 User Interface

### Design Features
- **Responsive Design**: Bootstrap 5 framework for mobile compatibility
- **Custom Styling**: Modern, clean interface with custom CSS
- **Google Fonts**: Lexend font family for improved readability
- **Interactive Elements**: Smooth animations and user feedback

### Navigation
- **Header Navigation**: Consistent navigation across all pages
- **User Authentication**: Clear login/logout states
- **Content Discovery**: Easy switching between newest and popular posts

## 🔄 Core Functionality

### Post Management
- **Rich Text Posts**: Support for text content with media
- **Multi-Media Upload**: Up to 5 files per post
- **Media Captions**: Optional captions for uploaded media
- **Content Validation**: Server-side validation for all content

### Social Interaction
- **Like System**: AJAX-powered like/unlike functionality
- **Engagement Display**: Real-time like and comment counts
- **User Profiles**: View other users' profiles and posts

## 🛠 Maintenance

### Regular Tasks
- **Database Cleanup**: Periodically clean up orphaned media files
- **Security Updates**: Keep PHP and MySQL updated
- **Backup Strategy**: Regular database and file backups
- **Log Monitoring**: Monitor error logs for issues

### Performance Optimization
- **Database Indexing**: Ensure proper indexes on frequently queried columns
- **Image Optimization**: Consider implementing image resizing
- **Caching**: Implement caching for frequently accessed data
