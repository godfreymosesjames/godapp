<?php
session_start();
ob_start();

// ==============================================
// GMotors - Premium Car Marketplace
// AUTO DATABASE CREATION - XAMPP LOCALHOST
// ==============================================

// Database Configuration - XAMPP Defaults
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // XAMPP default empty password
define('DB_NAME', 'gmotors_db');  // Will be auto-created

// Create upload directories
$upload_dirs = ['uploads/', 'uploads/cars/', 'uploads/admin/', 'uploads/about/', 'uploads/videos/', 'uploads/profiles/'];
foreach ($upload_dirs as $dir) {
    if (!file_exists($dir)) mkdir($dir, 0777, true);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================
// AUTO DATABASE CREATION FUNCTION
// ============================================
function autoCreateDatabase() {
    // First connect WITHOUT database to create it
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        return ['success' => false, 'message' => 'MySQL connection failed: ' . $conn->connect_error];
    }
    
    // Create database if not exists
    $db_name = DB_NAME;
    $create_db = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    if (!$conn->query($create_db)) {
        return ['success' => false, 'message' => 'Failed to create database: ' . $conn->error];
    }
    
    // Select the database
    $conn->select_db($db_name);
    
    // Create all tables
    $tables_sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(150),
            phone VARCHAR(50),
            address TEXT,
            profile_image VARCHAR(255),
            role ENUM('user', 'admin') DEFAULT 'user',
            status ENUM('active', 'pending', 'banned') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS cars (
            id INT AUTO_INCREMENT PRIMARY KEY,
            seller_id INT NOT NULL,
            brand VARCHAR(100) NOT NULL,
            model VARCHAR(100) NOT NULL,
            year INT NOT NULL,
            price DECIMAL(12,2) NOT NULL,
            mileage INT,
            fuel_type VARCHAR(50),
            transmission VARCHAR(50),
            color VARCHAR(50),
            image_path VARCHAR(255),
            image2_path VARCHAR(255),
            image3_path VARCHAR(255),
            car_card_path VARCHAR(255),
            description TEXT,
            seller_name VARCHAR(150),
            seller_phone VARCHAR(50),
            seller_location VARCHAR(200),
            status ENUM('available', 'sold', 'pending') DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            car_id INT NOT NULL,
            buyer_name VARCHAR(150) NOT NULL,
            buyer_email VARCHAR(150) NOT NULL,
            buyer_phone VARCHAR(50) NOT NULL,
            buyer_id INT,
            seller_id INT NOT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            total_amount DECIMAL(12,2) NOT NULL,
            gmotors_commission DECIMAL(12,2) DEFAULT 0,
            seller_amount DECIMAL(12,2) DEFAULT 0,
            commission_rate DECIMAL(5,2) DEFAULT 0,
            order_status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
            delivery_address TEXT NOT NULL,
            payment_method VARCHAR(50),
            notes TEXT,
            is_guest BOOLEAN DEFAULT TRUE,
            completed_date TIMESTAMP NULL,
            FOREIGN KEY (car_id) REFERENCES cars(id),
            FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (seller_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS gmotors_earnings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            car_id INT NOT NULL,
            seller_id INT NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            percentage DECIMAL(5,2) DEFAULT 30.00,
            status ENUM('pending', 'collected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (car_id) REFERENCES cars(id),
            FOREIGN KEY (seller_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            subject VARCHAR(200),
            message TEXT NOT NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id),
            FOREIGN KEY (receiver_id) REFERENCES users(id)
        );

        CREATE TABLE IF NOT EXISTS user_activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            username VARCHAR(100),
            action VARCHAR(100) NOT NULL,
            details TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(50),
            subject VARCHAR(200),
            message TEXT NOT NULL,
            reply TEXT,
            replied_by INT,
            replied_at TIMESTAMP NULL,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type VARCHAR(50) DEFAULT 'text',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS social_media (
            id INT AUTO_INCREMENT PRIMARY KEY,
            platform VARCHAR(50) NOT NULL,
            url VARCHAR(255) NOT NULL,
            icon VARCHAR(50),
            display_order INT DEFAULT 0
        );
    ";
    
    // Execute all table creation queries
    if ($conn->multi_query($tables_sql)) {
        do { 
            if ($result = $conn->store_result()) $result->free(); 
        } while ($conn->next_result());
    } else {
        return ['success' => false, 'message' => 'Failed to create tables: ' . $conn->error];
    }
    
    // ========== INSERT DEFAULT DATA ==========
    
    // Insert default admin
    $check_admin = $conn->query("SELECT * FROM users WHERE username = 'admin'");
    if ($check_admin && $check_admin->num_rows == 0) {
        $default_password = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, email, password, full_name, phone, role, status) VALUES 
                     ('admin', 'admin@gmotors.com', '$default_password', 'Administrator', '+255 700 000 000', 'admin', 'active')");
    }
    
    // Insert default settings
    $settings = [
        ['site_name', 'GMotors', 'text'],
        ['site_tagline', 'Premium Car Marketplace', 'text'],
        ['site_description', 'Tanzania\'s premier online car marketplace. Buy and sell quality cars with confidence.', 'textarea'],
        ['contact_email', 'info@gmotors.com', 'email'],
        ['contact_phone', '+255 700 000 000', 'text'],
        ['contact_address', 'Mwenge Mpakani, Dar es Salaam, Tanzania', 'text'],
        ['site_logo', '', 'text'],
        ['home_background', '', 'text'],
        ['home_video', '', 'text'],
        ['footer_image', '', 'text'],
        ['commission_percentage', '30', 'number'],
        ['about_hero_image', '', 'text'],
        ['about_story_image', '', 'text'],
        ['home_media_type', 'video', 'text']
    ];
    
    foreach ($settings as $setting) {
        $key = $conn->real_escape_string($setting[0]);
        $value = $conn->real_escape_string($setting[1]);
        $type = $conn->real_escape_string($setting[2]);
        $check = $conn->query("SELECT * FROM site_settings WHERE setting_key = '$key'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES ('$key', '$value', '$type')");
        }
    }
    
    // Insert social media
    $social = [
        ['Facebook', 'https://www.facebook.com/', 'fab fa-facebook-f', 1],
        ['Instagram', 'https://www.instagram.com/', 'fab fa-instagram', 2],
        ['TikTok', 'https://www.tiktok.com/', 'fab fa-tiktok', 3],
        ['YouTube', 'https://youtube.com/', 'fab fa-youtube', 4],
        ['WhatsApp', 'https://wa.me/', 'fab fa-whatsapp', 5],
        ['Twitter', 'https://twitter.com/', 'fab fa-twitter', 6]
    ];
    
    foreach ($social as $s) {
        $platform = $conn->real_escape_string($s[0]);
        $url = $conn->real_escape_string($s[1]);
        $icon = $conn->real_escape_string($s[2]);
        $order = intval($s[3]);
        $check = $conn->query("SELECT * FROM social_media WHERE platform = '$platform'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO social_media (platform, url, icon, display_order) VALUES ('$platform', '$url', '$icon', $order)");
        }
    }
    
    // Insert sample cars
    $check_cars = $conn->query("SELECT COUNT(*) as count FROM cars");
    if ($check_cars->fetch_assoc()['count'] == 0) {
        $conn->query("INSERT INTO cars (seller_id, brand, model, year, price, mileage, fuel_type, transmission, color, seller_name, seller_phone, description, image_path, image2_path, image3_path) VALUES 
            (1, 'Toyota', 'Land Cruiser', 2023, 85000000, 5000, 'Diesel', 'Automatic', 'White', 'GMotors', '+255 700 000 000', 'Excellent condition, fully loaded with service record available. Perfect for off-road adventures.', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500', 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=500', 'https://images.unsplash.com/photo-1568849676085-51415703900f?w=500'),
            (1, 'Honda', 'Fit', 2022, 18000000, 15000, 'Petrol', 'CVT', 'Silver', 'GMotors', '+255 700 000 000', 'Fuel efficient, spacious interior, perfect for city driving.', 'https://images.unsplash.com/photo-1604115525544-f7b1d0b2f72d?w=500', 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=500', 'https://images.unsplash.com/photo-1616423353971-0fd6e3cdb6a5?w=500'),
            (1, 'BMW', 'X5', 2023, 120000000, 3000, 'Petrol', 'Automatic', 'Black', 'GMotors', '+255 700 000 000', 'M Sport package, panoramic roof, like new condition. Direct from GMotors - no commission!', 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=500', 'https://images.unsplash.com/photo-1607853554439-0069ec0f29b6?w=500', 'https://images.unsplash.com/photo-1606664512398-d444bd57ed74?w=500')
        ");
    }
    
    $conn->close();
    return ['success' => true, 'message' => 'Database and tables created successfully!'];
}

// ============================================
// RUN AUTO DATABASE CREATION
// ============================================
$db_setup_result = autoCreateDatabase();

if (!$db_setup_result['success']) {
    die("
    <div style='max-width:800px; margin:50px auto; padding:30px; font-family:Arial, sans-serif; background:#f8f9fa; border-radius:10px; border:2px solid #dc3545;'>
        <h2 style='color:#dc3545;'>⚠️ Database Setup Error</h2>
        <p><strong>Error:</strong> " . htmlspecialchars($db_setup_result['message']) . "</p>
        <p><strong>Please check:</strong></p>
        <ul style='line-height:1.8;'>
            <li>XAMPP is running (Apache and MySQL)</li>
            <li>MySQL service is started in XAMPP Control Panel</li>
            <li>MySQL credentials are correct (default: root with empty password)</li>
        </ul>
        <p><strong>How to start XAMPP:</strong></p>
        <ol>
            <li>Open XAMPP Control Panel</li>
            <li>Click 'Start' next to Apache</li>
            <li>Click 'Start' next to MySQL</li>
            <li>Refresh this page</li>
        </ol>
    </div>
    ");
}

// ============================================
// CONNECT TO DATABASE (with database selected)
// ============================================
function getDatabaseConnection() {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

// Helper functions
function formatMoney($amount) {
    return 'TZS ' . number_format($amount, 0, ',', '.');
}

function getSetting($key, $conn) {
    if (!$conn) return '';
    $result = $conn->query("SELECT setting_value FROM site_settings WHERE setting_key = '$key'");
    if ($result && $row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    return '';
}

function getUserInfo($user_id, $conn) {
    if (!$conn) return null;
    $result = $conn->query("SELECT id, username, full_name, email, phone, address, profile_image FROM users WHERE id = $user_id");
    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}

function safeHtml($value) {
    return $value !== null ? htmlspecialchars($value) : '';
}

function logActivity($user_id, $username, $action, $details = null) {
    global $conn;
    if (!$conn) return;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $details = $details ? $conn->real_escape_string($details) : null;
    $username = $username ? $conn->real_escape_string($username) : 'Guest';
    $conn->query("INSERT INTO user_activities (user_id, username, action, details, ip_address) VALUES (" . ($user_id ?: 'NULL') . ", '$username', '$action', " . ($details ? "'$details'" : "NULL") . ", '$ip')");
}

// Upload functions
function uploadVideo($file) {
    $upload_dir = 'uploads/videos/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $filename = time() . '_' . uniqid() . '.' . $fileType;
    $target_file = $upload_dir . $filename;
    
    $video_types = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
    if (!in_array($fileType, $video_types)) {
        return ['success' => false, 'message' => 'Only MP4, WEBM, OGG, MOV, AVI files are allowed.'];
    }
    
    if ($file["size"] > 50000000) {
        return ['success' => false, 'message' => 'File too large. Max 50MB.'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'filename' => $filename, 'path' => $target_file];
    }
    return ['success' => false, 'message' => 'Upload error.'];
}

function uploadImage($file, $subdir = '') {
    $upload_dir = 'uploads/' . ($subdir ? $subdir . '/' : '');
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $filename = time() . '_' . uniqid() . '.' . $imageFileType;
    $target_file = $upload_dir . $filename;
    
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) return ['success' => false, 'message' => 'File is not an image.'];
    if ($file["size"] > 5000000) return ['success' => false, 'message' => 'File too large. Max 5MB.'];
    if (!in_array($imageFileType, ['jpg','jpeg','png','gif','webp'])) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG, WEBP & GIF allowed.'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'filename' => $filename, 'path' => $target_file];
    }
    return ['success' => false, 'message' => 'Upload error.'];
}

function uploadProfileImage($file) {
    $upload_dir = 'uploads/profiles/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $filename = time() . '_' . uniqid() . '.' . $imageFileType;
    $target_file = $upload_dir . $filename;
    
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) return ['success' => false, 'message' => 'File is not an image.'];
    if ($file["size"] > 2000000) return ['success' => false, 'message' => 'File too large. Max 2MB for profile picture.'];
    if (!in_array($imageFileType, ['jpg','jpeg','png','gif','webp'])) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG, WEBP & GIF allowed.'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'filename' => $filename, 'path' => $target_file];
    }
    return ['success' => false, 'message' => 'Upload error.'];
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Connect to database
$conn = getDatabaseConnection();
$mysql_connected = ($conn !== null);

if (!$mysql_connected) {
    die("Could not connect to database after creation. Please check MySQL is running in XAMPP.");
}

// ============================================
// POST HANDLERS
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mysql_connected) {
    
    // ========== UPDATE PROFILE PICTURE ==========
    if (isset($_POST['update_profile_pic']) && isset($_SESSION['user_id'])) {
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $result = uploadProfileImage($_FILES['profile_image']);
            if ($result['success']) {
                $conn->query("UPDATE users SET profile_image = '{$result['path']}' WHERE id = {$_SESSION['user_id']}");
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'update_profile_pic', "Updated profile picture");
                $_SESSION['form_message'] = "Profile picture updated successfully!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
        } else {
            $_SESSION['error_message'] = "No file uploaded!";
        }
        header('Location: ?page=profile');
        exit();
    }
    
    // ========== ADMIN BROADCAST MESSAGE ==========
    if (isset($_POST['admin_broadcast']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $subject = mysqli_real_escape_string($conn, $_POST['broadcast_subject']);
        $message = mysqli_real_escape_string($conn, $_POST['broadcast_message']);
        $admin_id = $_SESSION['user_id'];
        
        $users = $conn->query("SELECT id FROM users WHERE status = 'active' AND id != $admin_id");
        if ($users && $users->num_rows > 0) {
            while ($user = $users->fetch_assoc()) {
                $conn->query("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES ($admin_id, {$user['id']}, '$subject', '$message')");
            }
            logActivity($admin_id, $_SESSION['username'], 'admin_broadcast', "Sent broadcast message to all users: $subject");
            $_SESSION['form_message'] = "Broadcast message sent to all users!";
        } else {
            $_SESSION['error_message'] = "No users to send message to.";
        }
        header('Location: ?page=admin_messages');
        exit();
    }
    
    // ========== DELETE MESSAGE ==========
    if (isset($_POST['delete_message']) && isset($_SESSION['user_id'])) {
        $id = intval($_POST['message_id']);
        $user_id = $_SESSION['user_id'];
        $check = $conn->query("SELECT id FROM messages WHERE id = $id AND (sender_id = $user_id OR receiver_id = $user_id)");
        if ($check && $check->num_rows > 0) {
            $conn->query("DELETE FROM messages WHERE id = $id");
            logActivity($user_id, $_SESSION['username'], 'delete_message', "Deleted message ID: $id");
            $_SESSION['form_message'] = "Message deleted successfully!";
        } else {
            $_SESSION['error_message'] = "You don't have permission to delete this message.";
        }
        header('Location: ?page=messages');
        exit();
    }
    
    // ========== ADMIN UPDATE SETTINGS ==========
    if (isset($_POST['update_site_settings']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        foreach ($_POST['settings'] as $key => $value) {
            $key = mysqli_real_escape_string($conn, $key);
            $value = mysqli_real_escape_string($conn, $value);
            $conn->query("UPDATE site_settings SET setting_value = '$value' WHERE setting_key = '$key'");
        }
        $_SESSION['form_message'] = "Settings updated successfully!";
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN UPDATE SOCIAL MEDIA ==========
    if (isset($_POST['update_social_media']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        foreach ($_POST['social'] as $id => $url) {
            $id = intval($id);
            $url = mysqli_real_escape_string($conn, $url);
            $conn->query("UPDATE social_media SET url = '$url' WHERE id = $id");
        }
        $_SESSION['form_message'] = "Social media links updated successfully!";
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN ADD SOCIAL MEDIA ==========
    if (isset($_POST['add_social']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $platform = mysqli_real_escape_string($conn, $_POST['platform']);
        $url = mysqli_real_escape_string($conn, $_POST['url']);
        $icon = mysqli_real_escape_string($conn, $_POST['icon']);
        $order = intval($_POST['display_order']);
        $conn->query("INSERT INTO social_media (platform, url, icon, display_order) VALUES ('$platform', '$url', '$icon', $order)");
        $_SESSION['form_message'] = "Social media added successfully!";
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN DELETE SOCIAL MEDIA ==========
    if (isset($_POST['delete_social']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['social_id']);
        $conn->query("DELETE FROM social_media WHERE id = $id");
        $_SESSION['form_message'] = "Social media deleted successfully!";
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN UPLOAD VIDEO ==========
    if (isset($_POST['admin_upload_video']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        if (isset($_FILES['home_video']) && $_FILES['home_video']['error'] == 0) {
            $result = uploadVideo($_FILES['home_video']);
            if ($result['success']) {
                $conn->query("UPDATE site_settings SET setting_value = '{$result['path']}' WHERE setting_key = 'home_video'");
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_upload_video', "Uploaded new home video");
                $_SESSION['form_message'] = "Home video uploaded successfully!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
        } else {
            $_SESSION['error_message'] = "No video file uploaded!";
        }
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN UPLOAD LOGO ==========
    if (isset($_POST['admin_upload_logo']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $result = uploadImage($_FILES['logo'], 'admin');
            if ($result['success']) {
                $conn->query("UPDATE site_settings SET setting_value = '{$result['path']}' WHERE setting_key = 'site_logo'");
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_upload_logo', "Uploaded new logo");
                $_SESSION['form_message'] = "Logo uploaded successfully!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
        } else {
            $_SESSION['error_message'] = "No file uploaded!";
        }
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN UPLOAD BACKGROUND ==========
    if (isset($_POST['admin_upload_background']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        if (isset($_FILES['background']) && $_FILES['background']['error'] == 0) {
            $result = uploadImage($_FILES['background'], 'admin');
            if ($result['success']) {
                $conn->query("UPDATE site_settings SET setting_value = '{$result['path']}' WHERE setting_key = 'home_background'");
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_upload_background', "Uploaded new background");
                $_SESSION['form_message'] = "Background uploaded successfully!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
        } else {
            $_SESSION['error_message'] = "No file uploaded!";
        }
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN UPLOAD FOOTER IMAGE ==========
    if (isset($_POST['admin_upload_footer_image']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] == 0) {
            $result = uploadImage($_FILES['footer_image'], 'admin');
            if ($result['success']) {
                $conn->query("UPDATE site_settings SET setting_value = '{$result['path']}' WHERE setting_key = 'footer_image'");
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_upload_footer_image', "Uploaded new footer image");
                $_SESSION['form_message'] = "Footer image uploaded successfully!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
        } else {
            $_SESSION['error_message'] = "No file uploaded!";
        }
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN UPLOAD ABOUT HERO ==========
    if (isset($_POST['admin_upload_about_hero']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        if (isset($_FILES['about_hero']) && $_FILES['about_hero']['error'] == 0) {
            $result = uploadImage($_FILES['about_hero'], 'about');
            if ($result['success']) {
                $path = $conn->real_escape_string($result['path']);
                $check = $conn->query("SELECT * FROM site_settings WHERE setting_key = 'about_hero_image'");
                if ($check->num_rows > 0) {
                    $conn->query("UPDATE site_settings SET setting_value = '$path' WHERE setting_key = 'about_hero_image'");
                } else {
                    $conn->query("INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES ('about_hero_image', '$path', 'text')");
                }
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_upload_about_hero', "Uploaded about hero image");
                $_SESSION['form_message'] = "About hero image uploaded successfully!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
        } else {
            $_SESSION['error_message'] = "No file uploaded or upload error!";
        }
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN UPLOAD ABOUT STORY ==========
    if (isset($_POST['admin_upload_about_story']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        if (isset($_FILES['about_story']) && $_FILES['about_story']['error'] == 0) {
            $result = uploadImage($_FILES['about_story'], 'about');
            if ($result['success']) {
                $conn->query("UPDATE site_settings SET setting_value = '{$result['path']}' WHERE setting_key = 'about_story_image'");
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_upload_about_story', "Uploaded about story image");
                $_SESSION['form_message'] = "About story image uploaded successfully!";
            } else {
                $_SESSION['error_message'] = $result['message'];
            }
        } else {
            $_SESSION['error_message'] = "No file uploaded!";
        }
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== ADMIN SET MEDIA TYPE ==========
    if (isset($_POST['admin_set_media_type']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $media_type = mysqli_real_escape_string($conn, $_POST['media_type']);
        $conn->query("UPDATE site_settings SET setting_value = '$media_type' WHERE setting_key = 'home_media_type'");
        $_SESSION['form_message'] = "Media type updated to " . ucfirst($media_type) . "!";
        header('Location: ?page=admin_settings');
        exit();
    }
    
    // ========== CONTACT FORM ==========
    if (isset($_POST['submit_contact'])) {
        $name = mysqli_real_escape_string($conn, $_POST['contact_name']);
        $email = mysqli_real_escape_string($conn, $_POST['contact_email']);
        $phone = mysqli_real_escape_string($conn, $_POST['contact_phone'] ?? '');
        $subject = mysqli_real_escape_string($conn, $_POST['contact_subject']);
        $message = mysqli_real_escape_string($conn, $_POST['contact_message']);
        
        $sql = "INSERT INTO contacts (name, email, phone, subject, message, is_read) VALUES ('$name', '$email', '$phone', '$subject', '$message', 0)";
        if ($conn->query($sql)) {
            logActivity(null, $name, 'contact_form', "Contact message sent: $subject");
            $_SESSION['form_message'] = "Message sent successfully! We'll get back to you soon.";
        } else {
            $_SESSION['error_message'] = "Error sending message: " . $conn->error;
        }
        header('Location: ?page=contact');
        exit();
    }
    
    // ========== ADMIN REPLY CONTACT ==========
    if (isset($_POST['admin_reply_contact']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['contact_id']);
        $reply = mysqli_real_escape_string($conn, $_POST['reply']);
        $admin_id = $_SESSION['user_id'];
        
        $conn->query("UPDATE contacts SET reply = '$reply', replied_by = $admin_id, replied_at = NOW(), is_read = 1 WHERE id = $id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_reply_contact', "Replied to contact ID: $id");
        $_SESSION['form_message'] = "Reply sent successfully!";
        header('Location: ?page=admin_contacts');
        exit();
    }
    
    // ========== ADMIN DELETE CONTACT ==========
    if (isset($_POST['admin_delete_contact']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['contact_id']);
        $conn->query("DELETE FROM contacts WHERE id = $id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_contact', "Deleted contact ID: $id");
        $_SESSION['form_message'] = "Message deleted successfully!";
        header('Location: ?page=admin_contacts');
        exit();
    }
    
    // ========== ADMIN MARK READ ==========
    if (isset($_POST['admin_mark_read']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['contact_id']);
        $conn->query("UPDATE contacts SET is_read = 1 WHERE id = $id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_mark_read', "Marked message ID: $id as read");
        $_SESSION['form_message'] = "Message marked as read!";
        header('Location: ?page=admin_contacts');
        exit();
    }
    
    // ========== ADMIN DELETE CAR ==========
    if (isset($_POST['admin_delete_car']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['car_id']);
        $conn->query("DELETE FROM cars WHERE id = $id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_car', "Deleted car ID: $id");
        $_SESSION['form_message'] = "Car deleted successfully!";
        header('Location: ?page=admin_cars');
        exit();
    }
    
    // ========== ADMIN UPDATE CAR ==========
    if (isset($_POST['admin_update_car']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['car_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $price = floatval($_POST['price']);
        $conn->query("UPDATE cars SET status = '$status', price = $price WHERE id = $id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_update_car', "Updated car ID: $id");
        $_SESSION['form_message'] = "Car updated successfully!";
        header('Location: ?page=admin_cars');
        exit();
    }
    
    // ========== ADMIN UPDATE ORDER ==========
    if (isset($_POST['admin_update_order']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['order_id']);
        $status = mysqli_real_escape_string($conn, $_POST['order_status']);
        $conn->query("UPDATE orders SET order_status = '$status' WHERE id = $id");
        if ($status == 'completed') {
            $conn->query("UPDATE orders SET completed_date = NOW() WHERE id = $id");
            $conn->query("UPDATE gmotors_earnings SET status = 'collected' WHERE order_id = $id");
        }
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_update_order', "Updated order ID: $id");
        $_SESSION['form_message'] = "Order updated successfully!";
        header('Location: ?page=admin_orders');
        exit();
    }
    
    // ========== ADMIN DELETE ORDER ==========
    if (isset($_POST['admin_delete_order']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['order_id']);
        
        $conn->begin_transaction();
        
        try {
            $conn->query("DELETE FROM gmotors_earnings WHERE order_id = $id");
            
            $order = $conn->query("SELECT car_id FROM orders WHERE id = $id")->fetch_assoc();
            if ($order) {
                $car_id = $order['car_id'];
                $conn->query("UPDATE cars SET status = 'available' WHERE id = $car_id");
            }
            
            $conn->query("DELETE FROM orders WHERE id = $id");
            
            $conn->commit();
            logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_order', "Deleted order ID: $id");
            $_SESSION['form_message'] = "Order and associated earnings deleted successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = "Error deleting order: " . $e->getMessage();
        }
        
        header('Location: ?page=admin_orders');
        exit();
    }
    
    // ========== ADMIN DELETE EARNING DETAILS ==========
    if (isset($_POST['admin_delete_earning_detail']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['earning_id']);
        $earning = $conn->query("SELECT order_id FROM gmotors_earnings WHERE id = $id")->fetch_assoc();
        if ($earning) {
            $conn->query("DELETE FROM gmotors_earnings WHERE id = $id");
            $conn->query("UPDATE orders SET gmotors_commission = 0, commission_rate = 0 WHERE id = {$earning['order_id']}");
            logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_earning_detail', "Deleted earning record ID: $id");
            $_SESSION['form_message'] = "Earning record deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Earning record not found.";
        }
        header('Location: ?page=admin_earnings');
        exit();
    }
    
    // ========== ADMIN DELETE ALL COMMISSION ==========
    if (isset($_POST['admin_delete_all_commission']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $conn->query("DELETE FROM gmotors_earnings");
        $conn->query("UPDATE orders SET gmotors_commission = 0, commission_rate = 0 WHERE order_status = 'completed'");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_all_commission', "Deleted all commission records");
        $_SESSION['form_message'] = "All commission records deleted successfully!";
        header('Location: ?page=admin_earnings');
        exit();
    }
    
    // ========== ADMIN DELETE ACTIVITY ==========
    if (isset($_POST['admin_delete_activity']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['activity_id']);
        $conn->query("DELETE FROM user_activities WHERE id = $id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_activity', "Deleted activity ID: $id");
        $_SESSION['form_message'] = "Activity deleted successfully!";
        header('Location: ?page=admin_activities');
        exit();
    }
    
    // ========== ADMIN DELETE ALL ACTIVITIES ==========
    if (isset($_POST['admin_delete_all_activities']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $conn->query("DELETE FROM user_activities");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_all_activities', "Deleted all activities");
        $_SESSION['form_message'] = "All activities deleted successfully!";
        header('Location: ?page=admin_activities');
        exit();
    }
    
    // ========== ADMIN UPDATE USER ==========
    if (isset($_POST['admin_update_user']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['user_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        $conn->query("UPDATE users SET status = '$status', role = '$role' WHERE id = $id");
        logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_update_user', "Updated user ID: $id");
        $_SESSION['form_message'] = "User updated successfully!";
        header('Location: ?page=admin_users');
        exit();
    }
    
    // ========== ADMIN DELETE USER ==========
    if (isset($_POST['admin_delete_user']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $id = intval($_POST['user_id']);
        $check = $conn->query("SELECT role FROM users WHERE id = $id");
        $user = $check->fetch_assoc();
        if ($user['role'] == 'admin') {
            $_SESSION['error_message'] = "Cannot delete admin user!";
        } else {
            $conn->query("DELETE FROM users WHERE id = $id");
            logActivity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_user', "Deleted user ID: $id");
            $_SESSION['form_message'] = "User deleted successfully!";
        }
        header('Location: ?page=admin_users');
        exit();
    }
    
    // ========== USER REGISTRATION ==========
    if (isset($_POST['register'])) {
        $username = mysqli_real_escape_string($conn, $_POST['reg_username']);
        $email = mysqli_real_escape_string($conn, $_POST['reg_email']);
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);
        $full_name = mysqli_real_escape_string($conn, $_POST['reg_fullname']);
        $phone = mysqli_real_escape_string($conn, $_POST['reg_phone']);
        
        $check = $conn->query("SELECT id FROM users WHERE username = '$username' OR email = '$email'");
        if ($check->num_rows > 0) {
            $_SESSION['error_message'] = "Username or email already exists!";
        } else {
            $sql = "INSERT INTO users (username, email, password, full_name, phone, status) VALUES ('$username', '$email', '$password', '$full_name', '$phone', 'pending')";
            if ($conn->query($sql)) {
                $user_id = $conn->insert_id;
                logActivity($user_id, $username, 'register', "New user registered - pending approval");
                $_SESSION['form_message'] = "Registration successful! Awaiting admin approval.";
            } else {
                $_SESSION['error_message'] = "Registration failed: " . $conn->error;
            }
        }
        header('Location: ?page=login');
        exit();
    }
    
    // ========== USER LOGIN ==========
    if (isset($_POST['login'])) {
        $username = mysqli_real_escape_string($conn, $_POST['login_username']);
        $password = $_POST['login_password'];
        
        $result = $conn->query("SELECT * FROM users WHERE username = '$username' OR email = '$username'");
        if ($result && $user = $result->fetch_assoc()) {
            if ($user['status'] == 'pending') {
                $_SESSION['login_error'] = "Your account is pending admin approval.";
            } elseif ($user['status'] == 'banned') {
                $_SESSION['login_error'] = "Your account has been banned.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                logActivity($user['id'], $user['username'], 'login', "User logged in");
                $_SESSION['form_message'] = "Welcome back, " . $user['username'] . "!";
                header('Location: ?page=home');
                exit();
            } else {
                $_SESSION['login_error'] = "Invalid password!";
            }
        } else {
            $_SESSION['login_error'] = "User not found!";
        }
        header('Location: ?page=login');
        exit();
    }
    
    // ========== LOGOUT ==========
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: ?page=home');
        exit();
    }
    
    // ========== ADD CAR ==========
    if (isset($_POST['add_car'])) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Please login to sell a car.";
            header('Location: ?page=login');
            exit();
        }
        
        $seller_id = $_SESSION['user_id'];
        $seller_info = $conn->query("SELECT username, phone FROM users WHERE id = $seller_id")->fetch_assoc();
        
        $brand = mysqli_real_escape_string($conn, $_POST['brand']);
        $model = mysqli_real_escape_string($conn, $_POST['model']);
        $year = intval($_POST['year']);
        $price = floatval($_POST['price']);
        $mileage = intval($_POST['mileage'] ?? 0);
        $fuel_type = mysqli_real_escape_string($conn, $_POST['fuel_type']);
        $transmission = mysqli_real_escape_string($conn, $_POST['transmission']);
        $color = mysqli_real_escape_string($conn, $_POST['color']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $seller_location = mysqli_real_escape_string($conn, $_POST['seller_location'] ?? '');
        
        $image_path = $image2_path = $image3_path = $car_card_path = '';
        $upload_dir = 'uploads/cars/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        
        if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
            $filename = time() . '_1_' . basename($_FILES['car_image']['name']);
            if (move_uploaded_file($_FILES['car_image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = $upload_dir . $filename;
            }
        }
        if (isset($_FILES['car_image2']) && $_FILES['car_image2']['error'] == 0) {
            $filename = time() . '_2_' . basename($_FILES['car_image2']['name']);
            if (move_uploaded_file($_FILES['car_image2']['tmp_name'], $upload_dir . $filename)) {
                $image2_path = $upload_dir . $filename;
            }
        }
        if (isset($_FILES['car_image3']) && $_FILES['car_image3']['error'] == 0) {
            $filename = time() . '_3_' . basename($_FILES['car_image3']['name']);
            if (move_uploaded_file($_FILES['car_image3']['tmp_name'], $upload_dir . $filename)) {
                $image3_path = $upload_dir . $filename;
            }
        }
        if (isset($_FILES['car_card']) && $_FILES['car_card']['error'] == 0) {
            $filename = time() . '_card_' . basename($_FILES['car_card']['name']);
            if (move_uploaded_file($_FILES['car_card']['tmp_name'], $upload_dir . $filename)) {
                $car_card_path = $upload_dir . $filename;
            }
        }
        
        $sql = "INSERT INTO cars (seller_id, brand, model, year, price, mileage, fuel_type, transmission, color, image_path, image2_path, image3_path, car_card_path, description, seller_name, seller_phone, seller_location) 
                VALUES ($seller_id, '$brand', '$model', $year, $price, $mileage, '$fuel_type', '$transmission', '$color', '$image_path', '$image2_path', '$image3_path', '$car_card_path', '$description', '{$seller_info['username']}', '{$seller_info['phone']}', '$seller_location')";
        
        if ($conn->query($sql)) {
            logActivity($seller_id, $_SESSION['username'], 'add_car', "Added car: $brand $model");
            $_SESSION['form_message'] = "Car listed successfully!";
        } else {
            $_SESSION['error_message'] = "Error: " . $conn->error;
        }
        header('Location: ?page=my_cars');
        exit();
    }
    
    // ========== DELETE MY CAR ==========
    if (isset($_POST['delete_my_car'])) {
        if (!isset($_SESSION['user_id'])) exit;
        $car_id = intval($_POST['car_id']);
        $user_id = $_SESSION['user_id'];
        $conn->query("DELETE FROM cars WHERE id = $car_id AND seller_id = $user_id");
        logActivity($user_id, $_SESSION['username'], 'delete_car', "Deleted car ID: $car_id");
        $_SESSION['form_message'] = "Car deleted successfully.";
        header('Location: ?page=my_cars');
        exit();
    }
    
    // ========== PLACE ORDER ==========
    if (isset($_POST['place_order'])) {
        $car_id = intval($_POST['car_id']);
        $buyer_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        if ($buyer_id) {
            $user_info = getUserInfo($buyer_id, $conn);
            $buyer_name = $user_info['full_name'] ?: $user_info['username'];
            $buyer_email = $user_info['email'];
            $buyer_phone = $user_info['phone'];
            $delivery_address = $_POST['delivery_address'] ?: $user_info['address'];
        } else {
            $_SESSION['error_message'] = "Please login first to purchase a car.";
            header('Location: ?page=login');
            exit();
        }
        
        $delivery_address = mysqli_real_escape_string($conn, $_POST['delivery_address']);
        $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
        $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
        
        $car_result = $conn->query("SELECT c.*, u.role as seller_role FROM cars c JOIN users u ON c.seller_id = u.id WHERE c.id = $car_id AND c.status = 'available'");
        if ($car_result->num_rows == 0) {
            $_SESSION['error_message'] = "Car not available.";
            header('Location: ?page=cars');
            exit();
        }
        $car = $car_result->fetch_assoc();
        $seller_id = $car['seller_id'];
        $seller_role = $car['seller_role'];
        $commission = 0;
        $seller_amount = $car['price'];
        $commission_percentage = 0;
        
        if ($seller_role != 'admin') {
            $commission_percentage = 30;
            $commission = $car['price'] * ($commission_percentage / 100);
            $seller_amount = $car['price'] - $commission;
        }
        
        $sql = "INSERT INTO orders (car_id, buyer_name, buyer_email, buyer_phone, buyer_id, seller_id, total_amount, gmotors_commission, seller_amount, commission_rate, delivery_address, payment_method, notes, is_guest) 
                VALUES ($car_id, '$buyer_name', '$buyer_email', '$buyer_phone', " . ($buyer_id ?: 'NULL') . ", $seller_id, {$car['price']}, $commission, $seller_amount, $commission_percentage, '$delivery_address', '$payment_method', '$notes', " . ($buyer_id ? 'FALSE' : 'TRUE') . ")";
        
        if ($conn->query($sql)) {
            $order_id = $conn->insert_id;
            $conn->query("UPDATE cars SET status = 'sold' WHERE id = $car_id");
            if ($commission > 0) {
                $conn->query("INSERT INTO gmotors_earnings (order_id, car_id, seller_id, amount, percentage) VALUES ($order_id, $car_id, $seller_id, $commission, $commission_percentage)");
            }
            logActivity($buyer_id, $buyer_name, 'order_placed', "Order for car: {$car['brand']} {$car['model']}");
            $_SESSION['form_message'] = "Order placed successfully!";
            header('Location: ?page=order_success');
            exit();
        } else {
            $_SESSION['error_message'] = "Order failed: " . $conn->error;
            header('Location: ?page=cars');
            exit();
        }
    }
    
    // ========== SEND MESSAGE ==========
    if (isset($_POST['send_message'])) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Please login to send message.";
            header('Location: ?page=login');
            exit();
        }
        $sender_id = $_SESSION['user_id'];
        $receiver_id = intval($_POST['receiver_id']);
        $subject = mysqli_real_escape_string($conn, $_POST['subject']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        
        $sql = "INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES ($sender_id, $receiver_id, '$subject', '$message')";
        if ($conn->query($sql)) {
            logActivity($sender_id, $_SESSION['username'], 'send_message', "Sent message to user ID: $receiver_id");
            $_SESSION['form_message'] = "Message sent successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to send message.";
        }
        header('Location: ?page=messages');
        exit();
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $conn ? safeHtml(getSetting('site_name', $conn)) : 'GMotors'; ?> - Premium Car Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f0f4f8; color: #1e293b; }
        .container { max-width: 1300px; margin: 0 auto; padding: 0 30px; }
        
        .navbar { background: #0a1628; backdrop-filter: blur(10px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); position: fixed; width: 100%; top: 0; z-index: 1000; border-bottom: 2px solid #c9a84c; padding: 0.5rem 0; }
        .nav-container { display: flex; justify-content: space-between; align-items: center; height: 75px; }
        .logo { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; text-decoration: none; color: white; display: flex; align-items: center; gap: 12px; }
        .logo img { height: 55px; width: auto; object-fit: contain; }
        .logo i { color: #c9a84c; font-size: 32px; }
        .logo span { color: #c9a84c; }
        
        .nav-links { display: flex; gap: 5px; align-items: center; flex-wrap: wrap; }
        .nav-links a { text-decoration: none; color: #b8c5d6; font-weight: 500; font-size: 14px; padding: 6px 16px; border-radius: 40px; transition: 0.3s; }
        .nav-links a:hover { color: #c9a84c; background: rgba(201, 168, 76, 0.08); }
        .nav-links a.active { background: #c9a84c; color: #0a1628; box-shadow: 0 4px 15px rgba(201, 168, 76, 0.3); }
        .nav-links a i { margin-right: 5px; }
        
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; background: #0a1628; min-width: 200px; box-shadow: 0 8px 20px rgba(0,0,0,0.3); border-radius: 12px; padding: 8px 0; z-index: 1001; border: 1px solid rgba(201, 168, 76, 0.2); top: 100%; left: 0; }
        .dropdown-content a { display: block; padding: 10px 20px; color: #b8c5d6; text-decoration: none; font-size: 14px; border-radius: 0; }
        .dropdown-content a:hover { background: rgba(201, 168, 76, 0.1); color: #c9a84c; }
        .dropdown:hover .dropdown-content { display: block; }
        .dropdown:hover .dropbtn { color: #c9a84c; }
        
        .sell-btn { background: #c9a84c !important; color: #0a1628 !important; padding: 8px 24px !important; border-radius: 40px !important; font-weight: 700 !important; box-shadow: 0 4px 20px rgba(201, 168, 76, 0.3); }
        .sell-btn:hover { background: #b8973a !important; }
        
        .mobile-nav-toggle { display: none; background: none; border: none; font-size: 28px; color: white; cursor: pointer; }
        .mobile-nav { position: fixed; top: 75px; left: 0; width: 100%; background: #0a1628; padding: 20px; display: none; z-index: 999; border-bottom: 2px solid #c9a84c; max-height: calc(100vh - 75px); overflow-y: auto; }
        .mobile-nav.active { display: block; }
        .mobile-nav a { display: block; padding: 12px 20px; border-bottom: 1px solid rgba(255,255,255,0.05); text-decoration: none; color: #b8c5d6; }
        .mobile-nav a:hover { color: #c9a84c; }
        
        @media (max-width: 968px) { .nav-links { display: none; } .mobile-nav-toggle { display: flex; align-items: center; justify-content: center; } }
        
        .hero { min-height: 90vh; position: relative; display: flex; align-items: center; text-align: center; color: white; padding: 100px 0; overflow: hidden; }
        .hero-video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
        .hero-image { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; z-index: 0; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 22, 40, 0.65); z-index: 1; }
        .hero-content { position: relative; z-index: 2; max-width: 800px; margin: 0 auto; }
        .hero h1 { font-family: 'Playfair Display', serif; font-size: 60px; margin-bottom: 20px; }
        .hero h1 i { color: #c9a84c; }
        .hero p { font-size: 20px; margin-bottom: 40px; opacity: 0.9; }
        .btn-group { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        
        .btn { display: inline-block; padding: 14px 35px; background: #c9a84c; color: #0a1628; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 16px; transition: 0.3s; border: none; cursor: pointer; box-shadow: 0 4px 20px rgba(201, 168, 76, 0.3); }
        .btn:hover { background: #b8973a; transform: translateY(-3px); }
        .btn-secondary { background: transparent; border: 2px solid #c9a84c; color: #c9a84c; box-shadow: none; }
        .btn-secondary:hover { background: #c9a84c; color: #0a1628; }
        .btn-small { padding: 10px 20px; font-size: 14px; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #218838; }
        
        .section { padding: 80px 0; }
        .section-title { text-align: center; font-size: 40px; margin-bottom: 50px; padding-bottom: 15px; border-bottom: 4px solid #c9a84c; display: inline-block; font-family: 'Playfair Display', serif; }
        .section-title-center { text-align: center; display: block; }
        .bg-white { background: white; }
        
        .car-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .car-card { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: 0.4s; border: 1px solid #e2e8f0; }
        .car-card:hover { transform: translateY(-12px); box-shadow: 0 25px 50px rgba(201, 168, 76, 0.15); }
        .car-image { height: 220px; background-size: cover; background-position: center; position: relative; }
        .car-badge { position: absolute; top: 15px; right: 15px; background: #c9a84c; padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 12px; color: #0a1628; }
        .car-info { padding: 20px; }
        .car-title { font-size: 20px; font-weight: 700; }
        .car-price { font-size: 24px; font-weight: 800; color: #0a1628; margin: 8px 0; }
        .car-details { display: flex; gap: 15px; margin: 12px 0; color: #64748b; font-size: 13px; flex-wrap: wrap; }
        .car-details i { color: #c9a84c; }
        
        .form-card { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #334155; font-size: 14px; }
        input, textarea, select { width: 100%; padding: 14px 18px; border: 2px solid #e2e8f0; border-radius: 14px; font-size: 15px; transition: 0.3s; font-family: 'Poppins', sans-serif; background: #f8fafc; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1); background: white; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .file-upload { position: relative; display: inline-block; width: 100%; }
        .file-upload-label { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 18px; background: #f8fafc; border: 2px dashed #c9a84c; border-radius: 14px; cursor: pointer; transition: 0.3s; color: #64748b; font-weight: 500; }
        .file-upload-label:hover { background: #fef3c7; border-color: #b8973a; }
        .file-upload-label i { font-size: 28px; color: #c9a84c; }
        .file-upload-input { position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer; }
        
        .admin-dashboard { padding: 30px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #e2e8f0; }
        .admin-header h1 { font-size: 28px; font-family: 'Playfair Display', serif; }
        .admin-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .stat-card i { font-size: 40px; color: #c9a84c; margin-bottom: 15px; }
        .stat-number { font-size: 32px; font-weight: 700; color: #0a1628; }
        .admin-table { background: white; border-radius: 20px; padding: 20px; margin-bottom: 30px; overflow-x: auto; border: 1px solid #e2e8f0; }
        .admin-table h3 { margin-bottom: 20px; padding-bottom: 10px; border-bottom: 3px solid #c9a84c; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; font-weight: 600; }
        td { padding: 15px; border-bottom: 1px solid #e2e8f0; }
        
        .commission-delete-btn { background: #dc3545; color: white; padding: 8px 20px; border-radius: 30px; border: none; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .commission-delete-btn:hover { background: #c82333; transform: translateY(-2px); }
        .danger-zone { border: 2px solid #dc3545; border-radius: 12px; padding: 20px; margin-top: 20px; background: #fff5f5; }
        .danger-zone h4 { color: #dc3545; margin-bottom: 10px; }
        
        .footer { background: linear-gradient(rgba(10,22,40,0.85), rgba(10,22,40,0.9)), url('<?php 
            $footer_img = $conn ? getSetting('footer_image', $conn) : '';
            echo $footer_img && file_exists($footer_img) ? $footer_img : ''; 
        ?>'); background-size: cover; background-position: center; color: #b8c5d6; padding: 60px 0 30px; margin-top: 60px; position: relative; border-top: 4px solid #c9a84c; background-attachment: fixed; }
        .footer-content { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; margin-bottom: 40px; }
        .footer-brand h3 { font-family: 'Playfair Display', serif; font-size: 30px; color: white; }
        .footer-brand h3 i { color: #c9a84c; }
        .footer-brand p { color: #b8c5d6; line-height: 1.8; }
        .footer-social { display: flex; gap: 12px; margin-top: 20px; }
        .footer-social a { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: 0.3s; font-size: 18px; background: rgba(201, 168, 76, 0.2); }
        .footer-social a:hover { transform: translateY(-5px); background: #c9a84c; color: #0a1628; }
        .footer-links h4, .footer-hours h4, .footer-contact h4 { font-size: 18px; margin-bottom: 20px; color: #c9a84c; }
        .footer-links ul { list-style: none; }
        .footer-links ul li { margin-bottom: 10px; }
        .footer-links ul li a { color: #b8c5d6; text-decoration: none; transition: 0.3s; }
        .footer-links ul li a:hover { color: #c9a84c; }
        .hours-item { display: flex; justify-content: space-between; color: #b8c5d6; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 8px; }
        .hours-item .time { color: #c9a84c; }
        .contact-item { display: flex; align-items: center; gap: 15px; color: #b8c5d6; }
        .contact-item i { width: 35px; height: 35px; background: rgba(201, 168, 76, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #c9a84c; }
        .footer-bottom { padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .copyright { color: #b8c5d6; font-size: 14px; }
        
        .about-hero { background: linear-gradient(rgba(10,22,40,0.8), rgba(10,22,40,0.9)), url('<?php 
            $about_hero = $conn ? getSetting('about_hero_image', $conn) : '';
            echo $about_hero && file_exists($about_hero) ? $about_hero : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1600'; 
        ?>'); background-size: cover; background-position: center; color: white; padding: 100px 0; text-align: center; }
        .about-hero h1 { font-size: 48px; font-family: 'Playfair Display', serif; }
        .about-hero h1 i { color: #c9a84c; }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; margin-bottom: 60px; }
        .about-text h2 { font-size: 36px; font-family: 'Playfair Display', serif; border-bottom: 4px solid #c9a84c; display: inline-block; padding-bottom: 10px; color: #0a1628; }
        .about-text p { margin-bottom: 20px; line-height: 1.8; color: #475569; }
        .about-image { border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .about-image img { width: 100%; height: 100%; object-fit: cover; }
        .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin: 60px 0; }
        .value-card { background: white; padding: 40px 30px; border-radius: 24px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.08); transition: 0.3s; border: 1px solid #e2e8f0; }
        .value-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(201, 168, 76, 0.15); }
        .value-card i { font-size: 48px; color: #c9a84c; margin-bottom: 20px; }
        
        .toast { position: fixed; top: 90px; right: 20px; z-index: 9999; padding: 15px 25px; border-radius: 12px; color: white; font-weight: 500; animation: slideInRight 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.2); max-width: 350px; }
        .toast-success { background: #28a745; }
        .toast-error { background: #dc3545; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        
        .filter-bar { display: flex; gap: 15px; margin-bottom: 30px; flex-wrap: wrap; align-items: center; }
        .search-input { flex: 1; padding: 12px 20px; border: 2px solid #e2e8f0; border-radius: 40px; font-size: 15px; min-width: 200px; }
        .search-input:focus { outline: none; border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1); }
        
        .message-card { background: white; border-radius: 16px; padding: 1.2rem; margin-bottom: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: 0.3s; border: 1px solid #e2e8f0; }
        .message-unread { background: #fef3c7; border-left: 4px solid #c9a84c; }
        .message-subject { font-weight: 700; margin-bottom: 0.5rem; }
        .message-meta { font-size: 0.75rem; color: #64748b; margin-top: 0.5rem; }
        
        .profile-upload { text-align: center; }
        .profile-upload img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #c9a84c; margin-bottom: 15px; }
        
        .social-icons { display: flex; gap: 15px; flex-wrap: wrap; }
        .social-icon { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; text-decoration: none; transition: 0.3s; }
        .social-icon:hover { transform: translateY(-5px) scale(1.1); }
        .social-icon.facebook { background: #1877f2; }
        .social-icon.instagram { background: #d62976; }
        .social-icon.tiktok { background: #000000; }
        .social-icon.youtube { background: #ff0000; }
        .social-icon.whatsapp { background: #25d366; }
        .social-icon.twitter { background: #1da1f2; }
        
        .map-container { margin-top: 20px; border-radius: 16px; overflow: hidden; }
        .map-container iframe { width: 100%; height: 300px; border: 0; }
        
        @media (max-width: 968px) { .footer-content { grid-template-columns: repeat(2, 1fr); } .hero h1 { font-size: 40px; } }
        @media (max-width: 568px) { .footer-content { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } .car-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<?php
// Display session messages
if (isset($_SESSION['form_message'])): ?>
<div class="toast toast-success">
    <?php echo safeHtml($_SESSION['form_message']); ?>
    <button onclick="this.parentElement.style.display='none'" style="float:right; background:none; border:none; color:white; font-size:20px; cursor:pointer;">&times;</button>
</div>
<?php unset($_SESSION['form_message']); endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
<div class="toast toast-error">
    <?php echo safeHtml($_SESSION['error_message']); ?>
    <button onclick="this.parentElement.style.display='none'" style="float:right; background:none; border:none; color:white; font-size:20px; cursor:pointer;">&times;</button>
</div>
<?php unset($_SESSION['error_message']); endif; ?>

<?php if (isset($_SESSION['login_error'])): ?>
<div class="toast toast-error">
    <?php echo safeHtml($_SESSION['login_error']); ?>
    <button onclick="this.parentElement.style.display='none'" style="float:right; background:none; border:none; color:white; font-size:20px; cursor:pointer;">&times;</button>
</div>
<?php unset($_SESSION['login_error']); endif; ?>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container nav-container">
        <a href="?page=home" class="logo">
            <?php 
            $logo = $conn ? getSetting('site_logo', $conn) : '';
            if ($logo && file_exists($logo)): ?>
                <img src="<?php echo $logo; ?>" alt="GMotors Logo">
            <?php else: ?>
                <i class="fas fa-car"></i>
            <?php endif; ?>
            <span>GMotors</span>
        </a>
        <div class="nav-links">
            <a href="?page=home" class="<?php echo ($page == 'home') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Home</a>
            <a href="?page=cars" class="<?php echo ($page == 'cars') ? 'active' : ''; ?>"><i class="fas fa-car"></i> Cars</a>
            <a href="?page=about" class="<?php echo ($page == 'about') ? 'active' : ''; ?>"><i class="fas fa-info-circle"></i> About</a>
            <a href="?page=contact" class="<?php echo ($page == 'contact') ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Contact</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dropdown">
                    <a href="#" class="dropbtn"><i class="fas fa-user"></i> <?php echo safeHtml($_SESSION['username']); ?></a>
                    <div class="dropdown-content">
                        <a href="?page=profile"><i class="fas fa-id-card"></i> My Profile</a>
                        <a href="?page=my_cars"><i class="fas fa-list"></i> My Cars</a>
                        <a href="?page=my_orders"><i class="fas fa-shopping-cart"></i> My Orders</a>
                        <a href="?page=messages"><i class="fas fa-envelope"></i> Messages</a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                            <a href="?page=admin_dashboard"><i class="fas fa-chart-line"></i> Admin Panel</a>
                        <?php endif; ?>
                        <form method="POST" style="padding: 5px 20px;">
                            <button type="submit" name="logout" class="btn btn-small" style="background:#dc3545; color:white; width:100%;">Logout</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="?page=login" class="<?php echo ($page == 'login') ? 'active' : ''; ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="?page=register" class="<?php echo ($page == 'register') ? 'active' : ''; ?>"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
            
            <a href="?page=sell" class="sell-btn"><i class="fas fa-plus-circle"></i> Sell</a>
        </div>
        <button class="mobile-nav-toggle" onclick="toggleMobileNav()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div class="mobile-nav" id="mobileNav">
        <a href="?page=home">Home</a>
        <a href="?page=cars">Cars</a>
        <a href="?page=about">About</a>
        <a href="?page=contact">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="?page=profile">My Profile</a>
            <a href="?page=my_cars">My Cars</a>
            <a href="?page=my_orders">My Orders</a>
            <a href="?page=messages">Messages</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="?page=admin_dashboard">Admin Panel</a>
            <?php endif; ?>
            <form method="POST" style="padding:10px 20px;">
                <button type="submit" name="logout" class="btn btn-small" style="background:#dc3545; color:white; width:100%;">Logout</button>
            </form>
        <?php else: ?>
            <a href="?page=login">Login</a>
            <a href="?page=register">Register</a>
        <?php endif; ?>
        <a href="?page=sell" style="color:#c9a84c; font-weight:bold;">Sell Car</a>
    </div>
</nav>

<main style="margin-top: 75px;">

<?php
// ============================================
// HOME PAGE
// ============================================
if ($page == 'home') {
    $video_path = $conn ? getSetting('home_video', $conn) : '';
    $video_exists = $video_path && file_exists($video_path);
    $bg_image = $conn ? getSetting('home_background', $conn) : '';
    $bg_exists = $bg_image && file_exists($bg_image);
    $media_type = $conn ? getSetting('home_media_type', $conn) : 'video';
    $site_desc = $conn ? getSetting('site_description', $conn) : 'Tanzania\'s premier online car marketplace.';
?>
    <section class="hero">
        <?php if ($media_type == 'video' && $video_exists): ?>
            <video class="hero-video" autoplay muted loop playsinline>
                <source src="<?php echo $video_path; ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        <?php elseif ($media_type == 'image' && $bg_exists): ?>
            <div class="hero-image" style="background-image: url('<?php echo $bg_image; ?>');"></div>
        <?php else: ?>
            <div class="hero-image" style="background-image: url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1920');"></div>
        <?php endif; ?>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1><i class="fas fa-car"></i> Find Your <span style="color:#c9a84c;">Perfect</span> Ride</h1>
            <p><?php echo safeHtml($site_desc); ?></p>
            <div class="btn-group">
                <a href="?page=cars" class="btn"><i class="fas fa-search"></i> Browse Cars</a>
                <a href="?page=sell" class="btn btn-secondary"><i class="fas fa-plus-circle"></i> Sell Your Car</a>
            </div>
        </div>
    </section>
    
    <section class="section bg-white">
        <div class="container">
            <h2 class="section-title section-title-center">Featured Cars</h2>
            <div class="car-grid">
                <?php
                if ($conn) {
                    $featured = $conn->query("SELECT c.*, u.username as seller_username, u.role as seller_role FROM cars c JOIN users u ON c.seller_id = u.id WHERE c.status = 'available' ORDER BY c.created_at DESC LIMIT 6");
                    if ($featured && $featured->num_rows > 0) {
                        while ($car = $featured->fetch_assoc()) {
                ?>
                <div class="car-card">
                    <div class="car-image" style="background-image: url('<?php echo $car['image_path'] && file_exists($car['image_path']) ? $car['image_path'] : 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500'; ?>');">
                        <div class="car-badge">Available</div>
                    </div>
                    <div class="car-info">
                        <div class="car-title"><?php echo safeHtml($car['brand'] . ' ' . $car['model']); ?> (<?php echo $car['year']; ?>)</div>
                        <div class="car-price"><?php echo formatMoney($car['price']); ?></div>
                        <div class="car-details">
                            <span><i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['mileage'] ?? 0); ?> km</span>
                            <span><i class="fas fa-gas-pump"></i> <?php echo safeHtml($car['fuel_type']); ?></span>
                            <span><i class="fas fa-cog"></i> <?php echo safeHtml($car['transmission']); ?></span>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:12px;">
                            <a href="?page=car_detail&id=<?php echo $car['id']; ?>" class="btn btn-small" style="flex:1;">Details</a>
                            <a href="?page=buy&id=<?php echo $car['id']; ?>" class="btn btn-small btn-secondary" style="flex:1;">Buy Now</a>
                        </div>
                    </div>
                </div>
                <?php 
                        }
                    }
                }
                ?>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// CARS PAGE
// ============================================
} elseif ($page == 'cars' && $conn) {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $fuel = isset($_GET['fuel']) ? $_GET['fuel'] : '';
    $min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
    $max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 1000000000;
    
    $sql = "SELECT c.*, u.username as seller_username, u.role as seller_role FROM cars c JOIN users u ON c.seller_id = u.id WHERE c.status = 'available'";
    if ($search) $sql .= " AND (c.brand LIKE '%$search%' OR c.model LIKE '%$search%')";
    if ($fuel) $sql .= " AND c.fuel_type = '$fuel'";
    $sql .= " AND c.price >= $min_price AND c.price <= $max_price ORDER BY c.created_at DESC";
    $cars = $conn->query($sql);
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center">Browse All Cars</h2>
            <div class="filter-bar">
                <form method="GET" style="display:flex; gap:15px; flex-wrap:wrap; width:100%;">
                    <input type="hidden" name="page" value="cars">
                    <input type="text" name="search" class="search-input" placeholder="🔍 Search by brand or model..." value="<?php echo safeHtml($search); ?>">
                    <select name="fuel" style="padding:12px 20px; border:2px solid #e2e8f0; border-radius:40px; background:white;">
                        <option value="">All Fuel</option>
                        <option value="Petrol" <?php echo $fuel == 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                        <option value="Diesel" <?php echo $fuel == 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                        <option value="Electric" <?php echo $fuel == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                        <option value="Hybrid" <?php echo $fuel == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                    <input type="number" name="min_price" placeholder="Min Price" value="<?php echo $min_price; ?>" style="width:120px; padding:12px 15px; border:2px solid #e2e8f0; border-radius:40px;">
                    <input type="number" name="max_price" placeholder="Max Price" value="<?php echo $max_price; ?>" style="width:120px; padding:12px 15px; border:2px solid #e2e8f0; border-radius:40px;">
                    <button type="submit" class="btn btn-small"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
            <div class="car-grid">
                <?php if ($cars && $cars->num_rows > 0) {
                    while ($car = $cars->fetch_assoc()) { ?>
                <div class="car-card">
                    <div class="car-image" style="background-image: url('<?php echo $car['image_path'] && file_exists($car['image_path']) ? $car['image_path'] : 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500'; ?>');">
                        <div class="car-badge"><?php echo $car['status']; ?></div>
                    </div>
                    <div class="car-info">
                        <div class="car-title"><?php echo safeHtml($car['brand'] . ' ' . $car['model']); ?> (<?php echo $car['year']; ?>)</div>
                        <div class="car-price"><?php echo formatMoney($car['price']); ?></div>
                        <div class="car-details">
                            <span><i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['mileage'] ?? 0); ?> km</span>
                            <span><i class="fas fa-gas-pump"></i> <?php echo safeHtml($car['fuel_type']); ?></span>
                            <span><i class="fas fa-cog"></i> <?php echo safeHtml($car['transmission']); ?></span>
                            <span><i class="fas fa-user"></i> <?php echo $car['seller_role'] == 'admin' ? 'GMotors' : safeHtml($car['seller_username']); ?></span>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:12px;">
                            <a href="?page=car_detail&id=<?php echo $car['id']; ?>" class="btn btn-small" style="flex:1;">Details</a>
                            <a href="?page=buy&id=<?php echo $car['id']; ?>" class="btn btn-small btn-secondary" style="flex:1;">Buy Now</a>
                        </div>
                    </div>
                </div>
                <?php } } else { ?>
                <div style="grid-column:1/-1; text-align:center; padding:60px 0;">
                    <i class="fas fa-car" style="font-size:60px; color:#cbd5e1; margin-bottom:20px;"></i>
                    <h3>No Cars Found</h3>
                    <p style="color:#64748b;">Try adjusting your search filters.</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// CAR DETAIL
// ============================================
} elseif ($page == 'car_detail' && $conn && isset($_GET['id'])) {
    $car_id = intval($_GET['id']);
    $car = $conn->query("SELECT c.*, u.username as seller_username, u.role as seller_role FROM cars c JOIN users u ON c.seller_id = u.id WHERE c.id = $car_id")->fetch_assoc();
    if (!$car) { header('Location: ?page=cars'); exit(); }
    $images = [];
    if ($car['image_path'] && file_exists($car['image_path'])) $images[] = $car['image_path'];
    if ($car['image2_path'] && file_exists($car['image2_path'])) $images[] = $car['image2_path'];
    if ($car['image3_path'] && file_exists($car['image3_path'])) $images[] = $car['image3_path'];
    $main_image = !empty($images) ? $images[0] : 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800';
?>
    <section class="section">
        <div class="container">
            <a href="?page=cars" class="btn btn-small btn-secondary" style="margin-bottom:20px;"><i class="fas fa-arrow-left"></i> Back</a>
            <div style="display:grid;grid-template-columns:1fr 1fr; gap:50px;">
                <div>
                    <div style="border-radius:24px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1);">
                        <img id="mainCarImage" src="<?php echo $main_image; ?>" style="width:100%; height:400px; object-fit:cover;">
                    </div>
                    <div style="display:flex; gap:10px; margin-top:15px; flex-wrap:wrap;">
                        <?php foreach ($images as $index => $img) { ?>
                        <img src="<?php echo $img; ?>" style="width:80px; height:80px; object-fit:cover; border-radius:12px; cursor:pointer; border:2px solid <?php echo $index == 0 ? '#c9a84c' : '#e2e8f0'; ?>;" onclick="document.getElementById('mainCarImage').src='<?php echo $img; ?>'; document.querySelectorAll('.thumb').forEach(t => t.style.borderColor='#e2e8f0'); this.style.borderColor='#c9a84c';" class="thumb">
                        <?php } ?>
                    </div>
                </div>
                <div>
                    <h1 style="font-family:'Playfair Display',serif; font-size:36px;"><?php echo safeHtml($car['brand'] . ' ' . $car['model']); ?></h1>
                    <div class="car-price" style="font-size:32px;"><?php echo formatMoney($car['price']); ?></div>
                    <div style="display:flex; gap:15px; margin:20px 0; flex-wrap:wrap;">
                        <span class="status-badge <?php echo $car['status'] == 'available' ? 'status-available' : 'status-sold'; ?>"><?php echo ucfirst($car['status']); ?></span>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; background:#f8fafc; padding:20px; border-radius:16px; margin:20px 0;">
                        <div><strong>Year:</strong> <?php echo $car['year']; ?></div>
                        <div><strong>Mileage:</strong> <?php echo number_format($car['mileage'] ?? 0); ?> km</div>
                        <div><strong>Fuel:</strong> <?php echo safeHtml($car['fuel_type']); ?></div>
                        <div><strong>Transmission:</strong> <?php echo safeHtml($car['transmission']); ?></div>
                        <div><strong>Color:</strong> <?php echo safeHtml($car['color']); ?></div>
                        <div><strong>Seller:</strong> <?php echo $car['seller_role'] == 'admin' ? 'GMotors' : safeHtml($car['seller_username']); ?></div>
                    </div>
                    <div style="margin:20px 0;">
                        <h3>Description</h3>
                        <p style="color:#475569; line-height:1.8;"><?php echo nl2br(safeHtml($car['description'])); ?></p>
                    </div>
                    <div style="display:flex; gap:15px; margin-top:30px;">
                        <a href="?page=buy&id=<?php echo $car['id']; ?>" class="btn" style="flex:1;"><i class="fas fa-shopping-cart"></i> Buy Now</a>
                        <a href="?page=contact" class="btn btn-secondary" style="flex:1;"><i class="fas fa-envelope"></i> Enquire</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// BUY PAGE
// ============================================
} elseif ($page == 'buy' && $conn && isset($_GET['id'])) {
    $car_id = intval($_GET['id']);
    $car = $conn->query("SELECT c.*, u.username as seller_username, u.role as seller_role FROM cars c JOIN users u ON c.seller_id = u.id WHERE c.id = $car_id AND c.status = 'available'")->fetch_assoc();
    if (!$car) { header('Location: ?page=cars'); exit(); }
    
    $user_info = null;
    if (isset($_SESSION['user_id'])) {
        $user_info = getUserInfo($_SESSION['user_id'], $conn);
    }
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center"><i class="fas fa-shopping-cart"></i> Purchase Car</h2>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div style="background:#fef3c7; padding:15px; border-radius:12px; margin-bottom:20px; border-left:4px solid #c9a84c;">
                    <p><i class="fas fa-info-circle"></i> Please <a href="?page=login" style="color:#c9a84c; font-weight:bold;">login</a> to purchase this car. Your details will be auto-filled.</p>
                </div>
            <?php endif; ?>
            <div style="display:grid;grid-template-columns:1fr 1fr; gap:40px;">
                <div>
                    <div style="border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.08);">
                        <img src="<?php echo $car['image_path'] && file_exists($car['image_path']) ? $car['image_path'] : 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500'; ?>" style="width:100%; height:300px; object-fit:cover;">
                    </div>
                    <div style="margin-top:20px;">
                        <h3><?php echo safeHtml($car['brand'] . ' ' . $car['model']); ?> (<?php echo $car['year']; ?>)</h3>
                        <div class="car-price"><?php echo formatMoney($car['price']); ?></div>
                        <div class="car-details">
                            <span><i class="fas fa-tachometer-alt"></i> <?php echo number_format($car['mileage'] ?? 0); ?> km</span>
                            <span><i class="fas fa-gas-pump"></i> <?php echo safeHtml($car['fuel_type']); ?></span>
                            <span><i class="fas fa-cog"></i> <?php echo safeHtml($car['transmission']); ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-card">
                    <h3 style="margin-bottom:20px;">Order Details</h3>
                    <form method="POST" action="?page=place_order">
                        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="buyer_name" required value="<?php echo $user_info ? safeHtml($user_info['full_name'] ?: $user_info['username']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="buyer_email" required value="<?php echo $user_info ? safeHtml($user_info['email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Phone *</label>
                            <input type="tel" name="buyer_phone" required value="<?php echo $user_info ? safeHtml($user_info['phone']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Delivery Address *</label>
                            <textarea name="delivery_address" rows="3" required><?php echo $user_info ? safeHtml($user_info['address']) : ''; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Payment Method</label>
                            <select name="payment_method">
                                <option>M-Pesa</option><option>Bank Transfer</option><option>Cash on Delivery</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" name="place_order" class="btn" style="width:100%; padding:15px;">
                            <i class="fas fa-check"></i> Confirm Purchase
                        </button>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <p style="margin-top:10px; color:#64748b; font-size:13px; text-align:center;">
                                <i class="fas fa-lock"></i> You will be redirected to login if not logged in.
                            </p>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// PLACE ORDER HANDLER
// ============================================
} elseif ($page == 'place_order' && isset($_POST['place_order']) && $conn) {
    header('Location: ?page=order_success');
    exit();
    
} elseif ($page == 'order_success') { ?>
    <section class="section">
        <div class="container" style="text-align:center; padding:60px 0;">
            <i class="fas fa-check-circle" style="font-size:80px; color:#28a745; margin-bottom:30px;"></i>
            <h1 style="font-family:'Playfair Display',serif; font-size:48px;">Order Successful!</h1>
            <p style="color:#64748b; font-size:18px;">Thank you for your order. We will contact you shortly.</p>
            <a href="?page=cars" class="btn" style="margin-top:30px;"><i class="fas fa-search"></i> Browse More Cars</a>
        </div>
    </section>
    
<?php
// ============================================
// PROFILE PAGE
// ============================================
} elseif ($page == 'profile' && isset($_SESSION['user_id']) && $conn) {
    $user_id = $_SESSION['user_id'];
    $user = getUserInfo($user_id, $conn);
    if (!$user) {
        header('Location: ?page=home');
        exit();
    }
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center"><i class="fas fa-id-card"></i> My Profile</h2>
            <div style="display:grid; grid-template-columns:1fr 2fr; gap:30px;">
                <div class="form-card" style="text-align:center;">
                    <div class="profile-upload">
                        <?php if ($user['profile_image'] && file_exists($user['profile_image'])): ?>
                            <img src="<?php echo $user['profile_image']; ?>" alt="Profile Picture">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=c9a84c&color=fff&size=150" alt="Profile">
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="file-upload" style="margin:10px 0;">
                                <input type="file" name="profile_image" accept="image/*" class="file-upload-input">
                                <div class="file-upload-label" style="padding:12px;"><i class="fas fa-camera"></i> Change Photo</div>
                            </div>
                            <button type="submit" name="update_profile_pic" class="btn btn-small" style="width:100%;">Update Profile Picture</button>
                        </form>
                    </div>
                </div>
                <div class="form-card">
                    <h3>Account Details</h3>
                    <div style="display:grid; gap:15px;">
                        <div><strong>Username:</strong> <?php echo safeHtml($user['username']); ?></div>
                        <div><strong>Full Name:</strong> <?php echo safeHtml($user['full_name']); ?></div>
                        <div><strong>Email:</strong> <?php echo safeHtml($user['email']); ?></div>
                        <div><strong>Phone:</strong> <?php echo safeHtml($user['phone']); ?></div>
                        <div><strong>Address:</strong> <?php echo safeHtml($user['address']); ?></div>
                        <div><strong>Role:</strong> <?php echo ucfirst($_SESSION['role']); ?></div>
                        <div><strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'] ?? 'now')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// SELL CAR PAGE
// ============================================
} elseif ($page == 'sell' && $conn) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Please login to sell a car.";
        header('Location: ?page=login');
        exit();
    }
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center"><i class="fas fa-plus-circle"></i> Sell Your Car</h2>
            <p style="text-align:center; color:#64748b; margin-bottom:40px;">List your car and reach thousands of buyers.</p>
            <div class="form-card" style="max-width:800px; margin:0 auto;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group"><label>Brand *</label><input type="text" name="brand" required></div>
                        <div class="form-group"><label>Model *</label><input type="text" name="model" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Year *</label><input type="number" name="year" required></div>
                        <div class="form-group"><label>Price (TZS) *</label><input type="number" name="price" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Mileage (km)</label><input type="number" name="mileage"></div>
                        <div class="form-group"><label>Location</label><input type="text" name="seller_location"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Fuel Type</label>
                            <select name="fuel_type"><option>Petrol</option><option>Diesel</option><option>Electric</option><option>Hybrid</option></select>
                        </div>
                        <div class="form-group"><label>Transmission</label>
                            <select name="transmission"><option>Automatic</option><option>Manual</option><option>CVT</option></select>
                        </div>
                    </div>
                    <div class="form-group"><label>Color</label><input type="text" name="color"></div>
                    <div class="form-group">
                        <label>Main Image *</label>
                        <div class="file-upload">
                            <input type="file" name="car_image" accept="image/*" class="file-upload-input" required>
                            <div class="file-upload-label"><i class="fas fa-camera"></i> Upload Main Image</div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Image 2</label>
                            <div class="file-upload">
                                <input type="file" name="car_image2" accept="image/*" class="file-upload-input">
                                <div class="file-upload-label"><i class="fas fa-plus-circle"></i> Upload Image 2</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Image 3</label>
                            <div class="file-upload">
                                <input type="file" name="car_image3" accept="image/*" class="file-upload-input">
                                <div class="file-upload-label"><i class="fas fa-plus-circle"></i> Upload Image 3</div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Car Card (Security)</label>
                        <div class="file-upload">
                            <input type="file" name="car_card" accept="image/*" class="file-upload-input">
                            <div class="file-upload-label"><i class="fas fa-id-card"></i> Upload Car Card</div>
                        </div>
                        <small style="color:#64748b;">Only admin can view.</small>
                    </div>
                    <div class="form-group"><label>Description</label><textarea name="description" rows="4"></textarea></div>
                    <button type="submit" name="add_car" class="btn" style="width:100%; padding:15px;"><i class="fas fa-check"></i> List Car for Sale</button>
                </form>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// MY CARS
// ============================================
} elseif ($page == 'my_cars' && $conn) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Please login first.";
        header('Location: ?page=login');
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $cars = $conn->query("SELECT * FROM cars WHERE seller_id = $user_id ORDER BY created_at DESC");
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center"><i class="fas fa-list"></i> My Cars</h2>
            <div class="car-grid">
                <?php if ($cars && $cars->num_rows > 0) {
                    while ($car = $cars->fetch_assoc()) { ?>
                <div class="car-card">
                    <div class="car-image" style="background-image: url('<?php echo $car['image_path'] && file_exists($car['image_path']) ? $car['image_path'] : 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500'; ?>');">
                        <div class="car-badge"><?php echo $car['status']; ?></div>
                    </div>
                    <div class="car-info">
                        <div class="car-title"><?php echo safeHtml($car['brand'] . ' ' . $car['model']); ?> (<?php echo $car['year']; ?>)</div>
                        <div class="car-price"><?php echo formatMoney($car['price']); ?></div>
                        <form method="POST" style="margin-top:12px;">
                            <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                            <button type="submit" name="delete_my_car" class="btn btn-danger btn-small" onclick="return confirm('Delete this car?')"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
                <?php } } else { ?>
                <div style="grid-column:1/-1; text-align:center; padding:60px 0;">
                    <i class="fas fa-car" style="font-size:60px; color:#cbd5e1; margin-bottom:20px;"></i>
                    <h3>No Cars Listed</h3>
                    <a href="?page=sell" class="btn" style="margin-top:20px;"><i class="fas fa-plus-circle"></i> Sell Your Car</a>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// MY ORDERS
// ============================================
} elseif ($page == 'my_orders' && $conn) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Please login first.";
        header('Location: ?page=login');
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $orders = $conn->query("SELECT o.*, c.brand, c.model, c.year, c.image_path, c.image2_path, c.image3_path, c.car_card_path FROM orders o JOIN cars c ON o.car_id = c.id WHERE o.buyer_id = $user_id ORDER BY o.order_date DESC");
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center"><i class="fas fa-shopping-cart"></i> My Orders</h2>
            <div class="car-grid">
                <?php if ($orders && $orders->num_rows > 0) {
                    while ($order = $orders->fetch_assoc()) {
                        $car_image = $order['car_card_path'] && file_exists($order['car_card_path']) 
                            ? $order['car_card_path'] 
                            : ($order['image_path'] && file_exists($order['image_path']) 
                                ? $order['image_path'] 
                                : 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500');
                    ?>
                <div class="car-card">
                    <div class="car-image" style="background-image: url('<?php echo $car_image; ?>');">
                        <div class="car-badge">Order #<?php echo $order['id']; ?></div>
                    </div>
                    <div class="car-info">
                        <div class="car-title"><?php echo safeHtml($order['brand'] . ' ' . $order['model']); ?> (<?php echo $order['year']; ?>)</div>
                        <div class="car-price"><?php echo formatMoney($order['total_amount']); ?></div>
                        <div class="car-details">
                            <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($order['order_date'])); ?></span>
                            <span><i class="fas fa-check-circle" style="color:#27ae60;"></i> Status: <?php echo ucfirst($order['order_status']); ?></span>
                        </div>
                        <div style="background:#f8f9fa; padding:1.2rem; border-radius:12px; margin-top:1rem; border-left:4px solid #c9a84c;">
                            <i class="fas fa-info-circle" style="color:#c9a84c; font-size:20px; float:left; margin-right:10px;"></i>
                            <div>
                                <strong style="color:#0a1628; font-size:16px;">Your car is being ordered.</strong>
                                <p style="color:#475569; margin-top:5px; font-size:14px;">Contact GMotors for more information.</p>
                                <p style="color:#64748b; font-size:12px; margin-top:8px;">
                                    <i class="fas fa-phone"></i> Call: <?php echo getSetting('contact_phone', $conn) ?: '+255 700 000 000'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } } else { ?>
                <div style="grid-column:1/-1; text-align:center; padding:60px 0;">
                    <i class="fas fa-shopping-cart" style="font-size:60px; color:#cbd5e1; margin-bottom:20px;"></i>
                    <h3>No Orders Yet</h3>
                    <p style="color:#64748b;">You haven't placed any orders yet.</p>
                    <a href="?page=cars" class="btn" style="margin-top:20px;"><i class="fas fa-search"></i> Browse Cars</a>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// LOGIN PAGE
// ============================================
} elseif ($page == 'login') { ?>
    <section class="section">
        <div class="container">
            <div class="form-card" style="max-width:500px; margin:0 auto;">
                <h2 class="section-title section-title-center" style="font-size:32px;">Login</h2>
                <form method="POST">
                    <div class="form-group"><label>Username or Email</label><input type="text" name="login_username" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="login_password" required></div>
                    <button type="submit" name="login" class="btn" style="width:100%; padding:15px;">Login</button>
                    <p style="text-align:center; margin-top:15px;">Don't have an account? <a href="?page=register" style="color:#c9a84c;">Register</a></p>
                </form>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// REGISTER PAGE
// ============================================
} elseif ($page == 'register') { ?>
    <section class="section">
        <div class="container">
            <div class="form-card" style="max-width:500px; margin:0 auto;">
                <h2 class="section-title section-title-center" style="font-size:32px;">Register</h2>
                <form method="POST">
                    <div class="form-group"><label>Username *</label><input type="text" name="reg_username" required></div>
                    <div class="form-group"><label>Email *</label><input type="email" name="reg_email" required></div>
                    <div class="form-group"><label>Full Name</label><input type="text" name="reg_fullname"></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="reg_phone"></div>
                    <div class="form-group"><label>Password *</label><input type="password" name="reg_password" required></div>
                    <button type="submit" name="register" class="btn" style="width:100%; padding:15px;">Register</button>
                    <p style="text-align:center; margin-top:15px;">Already have an account? <a href="?page=login" style="color:#c9a84c;">Login</a></p>
                    <p style="text-align:center; color:#64748b; font-size:13px;">Account needs admin approval before use.</p>
                </form>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// ABOUT PAGE
// ============================================
} elseif ($page == 'about') {
    $hero_image = $conn ? getSetting('about_hero_image', $conn) : '';
    $story_image = $conn ? getSetting('about_story_image', $conn) : '';
    $social = $conn ? $conn->query("SELECT * FROM social_media ORDER BY display_order") : null;
?>
    <section class="about-hero" style="background: linear-gradient(rgba(10,22,40,0.8), rgba(10,22,40,0.9)), url('<?php echo $hero_image && file_exists($hero_image) ? $hero_image : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1600'; ?>'); background-size: cover; background-position: center;">
        <div class="container">
            <h1><i class="fas fa-info-circle"></i> About GMotors</h1>
            <p style="font-size:20px;">Tanzania's premier online car marketplace</p>
        </div>
    </section>
    
    <section class="section">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h2>Our Story</h2>
                    <p>GMotors was founded in 2020 with a mission: to make car buying and selling in Tanzania easy, transparent, and secure. What started as a small team has grown into Tanzania's premier online car marketplace.</p>
                    <p>We've helped thousands of customers find their dream cars and connect with trusted sellers. Our platform combines cutting-edge technology with local expertise.</p>
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-top:30px;">
                        <div style="text-align:center;"><div style="font-size:32px; font-weight:700; color:#c9a84c;">500+</div><div style="color:#64748b;">Cars Sold</div></div>
                        <div style="text-align:center;"><div style="font-size:32px; font-weight:700; color:#c9a84c;">98%</div><div style="color:#64748b;">Satisfaction</div></div>
                        <div style="text-align:center;"><div style="font-size:32px; font-weight:700; color:#c9a84c;">4.8★</div><div style="color:#64748b;">Rating</div></div>
                    </div>
                </div>
                <div class="about-image">
                    <img src="<?php echo $story_image && file_exists($story_image) ? $story_image : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800'; ?>" alt="GMotors Story">
                </div>
            </div>
            
            <h2 class="section-title section-title-center">Our Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Trust & Security</h3>
                    <p style="color:#64748b;">All transactions are secure and transparent.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-handshake"></i>
                    <h3>Customer First</h3>
                    <p style="color:#64748b;">Your satisfaction is our priority.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-rocket"></i>
                    <h3>Innovation</h3>
                    <p style="color:#64748b;">We continuously improve our platform.</p>
                </div>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// CONTACT PAGE
// ============================================
} elseif ($page == 'contact') {
    $social = $conn ? $conn->query("SELECT * FROM social_media ORDER BY display_order") : null;
    $address = $conn ? getSetting('contact_address', $conn) : 'Mwenge Mpakani, Dar es Salaam';
    $phone = $conn ? getSetting('contact_phone', $conn) : '+255 700 000 000';
    $email = $conn ? getSetting('contact_email', $conn) : 'info@gmotors.com';
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center">Contact Us</h2>
            <p style="text-align:center; color:#64748b; margin-bottom:40px;">We'd love to hear from you. Fill out the form below and we'll get back to you.</p>
            
            <div style="display:grid; grid-template-columns:2fr 1fr; gap:30px;">
                <div class="form-card">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="contact_name" required placeholder="Enter your full name">
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="contact_email" required placeholder="Enter your email address">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="contact_phone" placeholder="Enter your phone number">
                            </div>
                            <div class="form-group">
                                <label>Subject *</label>
                                <input type="text" name="contact_subject" required placeholder="Message subject">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea name="contact_message" rows="6" required placeholder="Write your message here..."></textarea>
                        </div>
                        <button type="submit" name="submit_contact" class="btn" style="width:100%; padding:15px; font-size:18px;">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
                
                <div>
                    <div style="background:white; padding:25px; border-radius:20px; margin-bottom:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); border:1px solid #e2e8f0;">
                        <h3><i class="fas fa-map-marker-alt" style="color:#c9a84c;"></i> Address</h3>
                        <p style="color:#64748b;"><?php echo safeHtml($address); ?></p>
                    </div>
                    
                    <div style="background:white; padding:25px; border-radius:20px; margin-bottom:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); border:1px solid #e2e8f0;">
                        <h3><i class="fas fa-phone" style="color:#c9a84c;"></i> Phone</h3>
                        <p style="color:#64748b;"><a href="tel:<?php echo safeHtml($phone); ?>" style="color:#0a1628;"><?php echo safeHtml($phone); ?></a></p>
                    </div>
                    
                    <div style="background:white; padding:25px; border-radius:20px; margin-bottom:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); border:1px solid #e2e8f0;">
                        <h3><i class="fas fa-envelope" style="color:#c9a84c;"></i> Email</h3>
                        <p style="color:#64748b;"><a href="mailto:<?php echo safeHtml($email); ?>" style="color:#0a1628;"><?php echo safeHtml($email); ?></a></p>
                    </div>
                    
                    <div style="background:white; padding:25px; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); border:1px solid #e2e8f0;">
                        <h3><i class="fas fa-share-alt" style="color:#c9a84c;"></i> Follow Us</h3>
                        <div class="social-icons">
                            <?php if ($social && $social->num_rows > 0) { 
                                while ($s = $social->fetch_assoc()) { 
                                    $bg = '#333';
                                    $plat = strtolower($s['platform']);
                                    if ($plat == 'facebook') $bg = '#1877f2';
                                    elseif ($plat == 'instagram') $bg = '#d62976';
                                    elseif ($plat == 'tiktok') $bg = '#000000';
                                    elseif ($plat == 'youtube') $bg = '#ff0000';
                                    elseif ($plat == 'whatsapp') $bg = '#25d366';
                                    elseif ($plat == 'twitter') $bg = '#1da1f2';
                            ?>
                            <a href="<?php echo safeHtml($s['url'] ?? '#'); ?>" target="_blank" class="social-icon" style="background:<?php echo $bg; ?>;">
                                <i class="<?php echo safeHtml($s['icon'] ?? 'fas fa-link'); ?>"></i>
                            </a>
                            <?php } } ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="map-container" style="margin-top:30px;">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.863562843483!2d39.25129731477362!3d-6.800573195047762!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x185c4b6e5f6e6e6f%3A0x2e6e6e6e6e6e6e6e!2sMwenge%2C%20Dar%20es%20Salaam%2C%20Tanzania!5e0!3m2!1sen!2s!4v1700000000000!5m2!1sen!2s" allowfullscreen loading="lazy"></iframe>
            </div>
        </div>
    </section>
    
<?php
// ============================================
// ADMIN DASHBOARD
// ============================================
} elseif ($page == 'admin_dashboard' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && $conn) {
    $total_cars = $conn->query("SELECT COUNT(*) as count FROM cars")->fetch_assoc()['count'];
    $total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
    $available_cars = $conn->query("SELECT COUNT(*) as count FROM cars WHERE status = 'available'")->fetch_assoc()['count'];
    $pending_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'pending'")->fetch_assoc()['count'];
    $total_commission = $conn->query("SELECT SUM(gmotors_commission) as total FROM orders WHERE order_status = 'completed'")->fetch_assoc()['total'] ?? 0;
    $unread_messages = $conn->query("SELECT COUNT(*) as count FROM contacts WHERE is_read = 0")->fetch_assoc()['count'];
    $total_activities = $conn->query("SELECT COUNT(*) as count FROM user_activities")->fetch_assoc()['count'];
    $total_earnings = $conn->query("SELECT COUNT(*) as count FROM gmotors_earnings")->fetch_assoc()['count'];
?>
    <div class="admin-dashboard">
        <div class="container">
            <div class="admin-header">
                <h1><i class="fas fa-chart-line" style="color:#c9a84c;"></i> Admin Dashboard</h1>
                <form method="POST">
                    <button type="submit" name="logout" class="btn btn-small" style="background:#dc3545; color:white;">Logout</button>
                </form>
            </div>
            
            <div class="admin-stats">
                <div class="stat-card"><i class="fas fa-car"></i><div class="stat-number"><?php echo $total_cars; ?></div><div>Total Cars</div></div>
                <div class="stat-card"><i class="fas fa-check-circle" style="color:#28a745;"></i><div class="stat-number"><?php echo $available_cars; ?></div><div>Available</div></div>
                <div class="stat-card"><i class="fas fa-shopping-cart"></i><div class="stat-number"><?php echo $total_orders; ?></div><div>Total Orders</div></div>
                <div class="stat-card"><i class="fas fa-users"></i><div class="stat-number"><?php echo $pending_users; ?></div><div>Pending Users</div></div>
                <div class="stat-card"><i class="fas fa-money-bill-wave"></i><div class="stat-number"><?php echo formatMoney($total_commission); ?></div><div>Commission Earned</div></div>
                <div class="stat-card"><i class="fas fa-envelope"></i><div class="stat-number"><?php echo $unread_messages; ?></div><div>Unread Messages</div></div>
                <div class="stat-card"><i class="fas fa-clipboard-list"></i><div class="stat-number"><?php echo $total_activities; ?></div><div>Activities</div></div>
                <div class="stat-card"><i class="fas fa-percent" style="color:#c9a84c;"></i><div class="stat-number"><?php echo $total_earnings; ?></div><div>Earnings Records</div></div>
            </div>
            
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:15px; margin-bottom:30px;">
                <a href="?page=admin_cars" class="btn" style="text-align:center;">Manage Cars</a>
                <a href="?page=admin_users" class="btn" style="text-align:center;">Manage Users</a>
                <a href="?page=admin_orders" class="btn" style="text-align:center;">Manage Orders</a>
                <a href="?page=admin_contacts" class="btn" style="text-align:center;">Contacts</a>
                <a href="?page=admin_earnings" class="btn" style="text-align:center;">Earnings</a>
                <a href="?page=admin_activities" class="btn" style="text-align:center;">Activities</a>
                <a href="?page=admin_settings" class="btn" style="text-align:center;">Settings</a>
                <a href="?page=admin_messages" class="btn" style="text-align:center;">Messages</a>
            </div>
            
            <div class="danger-zone">
                <h4><i class="fas fa-exclamation-triangle"></i> Danger Zone - Commission Management</h4>
                <p style="color:#64748b; margin-bottom:15px;">Delete all commission records from the system. This action cannot be undone.</p>
                <form method="POST" onsubmit="return confirm('⚠️ WARNING: This will delete ALL commission records permanently. Are you sure?')">
                    <button type="submit" name="admin_delete_all_commission" class="commission-delete-btn">
                        <i class="fas fa-trash-alt"></i> Delete ALL Commission Records
                    </button>
                </form>
            </div>
        </div>
    </div>
    
<?php
// ============================================
// ADMIN EARNINGS
// ============================================
} elseif ($page == 'admin_earnings' && isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && $conn) {
    $earnings = $conn->query("SELECT o.*, c.brand, c.model, u.username as seller_name FROM orders o JOIN cars c ON o.car_id = c.id JOIN users u ON o.seller_id = u.id WHERE o.order_status = 'completed' ORDER BY o.order_date DESC");
    $total_commission = $conn->query("SELECT SUM(gmotors_commission) as total FROM orders WHERE order_status = 'completed'")->fetch_assoc()['total'] ?? 0;
    $total_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE order_status = 'completed'")->fetch_assoc()['total'] ?? 0;
    $earning_count = $conn->query("SELECT COUNT(*) as count FROM gmotors_earnings")->fetch_assoc()['count'];
?>
    <div class="admin-dashboard">
        <div class="container">
            <div class="admin-header">
                <h1><i class="fas fa-money-bill-wave" style="color:#c9a84c;"></i> Earnings</h1>
                <a href="?page=admin_dashboard" class="btn btn-small">Back</a>
            </div>
            <div class="admin-stats">
                <div class="stat-card"><i class="fas fa-car"></i><div class="stat-number"><?php echo $earnings->num_rows; ?></div><div>Completed Sales</div></div>
                <div class="stat-card"><i class="fas fa-money-bill"></i><div class="stat-number"><?php echo formatMoney($total_sales); ?></div><div>Total Revenue</div></div>
                <div class="stat-card"><i class="fas fa-percent" style="color:#c9a84c;"></i><div class="stat-number"><?php echo formatMoney($total_commission); ?></div><div>Commission Earned</div></div>
                <div class="stat-card"><i class="fas fa-list"></i><div class="stat-number"><?php echo $earning_count; ?></div><div>Earnings Records</div></div>
            </div>
            
            <div class="danger-zone" style="margin-bottom:20px;">
                <h4><i class="fas fa-exclamation-triangle"></i> Delete All Commission Records</h4>
                <p style="color:#64748b; margin-bottom:15px;">This will permanently delete ALL commission records from the system.</p>
                <form method="POST" onsubmit="return confirm('⚠️ WARNING: This will delete ALL commission records permanently. Are you sure?')">
                    <button type="submit" name="admin_delete_all_commission" class="commission-delete-btn">
                        <i class="fas fa-trash-alt"></i> Delete ALL Commission Records
                    </button>
                </form>
            </div>
            
            <div class="admin-table">
                <h3>Earnings Details</h3>
                <table>
                    <thead><tr><th>ID</th><th>Car</th><th>Buyer</th><th>Amount</th><th>Commission</th><th>Type</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($earnings && $earnings->num_rows > 0) {
                            while ($e = $earnings->fetch_assoc()) {
                                $type = $e['gmotors_commission'] > 0 ? 'User Sale (30%)' : 'Admin Direct';
                        ?>
                        <tr>
                            <td>#<?php echo $e['id']; ?></td>
                            <td><?php echo safeHtml($e['brand'] . ' ' . $e['model']); ?></td>
                            <td><?php echo safeHtml($e['buyer_name']); ?></td>
                            <td><?php echo formatMoney($e['total_amount']); ?></td>
                            <td><?php echo $e['gmotors_commission'] > 0 ? formatMoney($e['gmotors_commission']) : '-'; ?></td>
                            <td><span class="status-badge <?php echo $e['gmotors_commission'] > 0 ? 'status-available' : 'status-completed'; ?>"><?php echo $type; ?></span></td>
                            <td><?php echo date('M d', strtotime($e['order_date'])); ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this earning record?')">
                                    <input type="hidden" name="earning_id" value="<?php echo $e['id']; ?>">
                                    <button type="submit" name="admin_delete_earning_detail" class="action-btn delete-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
<?php
// ============================================
// MESSAGES PAGE
// ============================================
} elseif ($page == 'messages' && $conn) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Please login first.";
        header('Location: ?page=login');
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $inbox = $conn->query("SELECT m.*, u.username as sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = $user_id ORDER BY m.created_at DESC");
    $users = $conn->query("SELECT id, username, role FROM users WHERE id != $user_id AND status = 'active'");
    $contact_messages = $conn->query("SELECT * FROM contacts WHERE email IN (SELECT email FROM users WHERE id = $user_id) OR name = (SELECT username FROM users WHERE id = $user_id) ORDER BY created_at DESC");
?>
    <section class="section">
        <div class="container">
            <h2 class="section-title section-title-center"><i class="fas fa-envelope"></i> Messages</h2>
            
            <div style="display:grid;grid-template-columns:2fr 1fr; gap:30px;">
                <div class="form-card">
                    <h3>Inbox</h3>
                    <?php 
                    $has_messages = false;
                    
                    if ($contact_messages && $contact_messages->num_rows > 0) {
                        while ($msg = $contact_messages->fetch_assoc()) {
                            $has_messages = true;
                            $is_unread = !$msg['is_read'];
                    ?>
                    <div class="message-card <?php echo $is_unread ? 'message-unread' : ''; ?>" style="border-left: 4px solid <?php echo $msg['reply'] ? '#28a745' : ($is_unread ? '#c9a84c' : '#e2e8f0'); ?>;">
                        <div class="message-subject">
                            <?php echo safeHtml($msg['subject']); ?>
                            <?php if ($msg['reply']) { ?>
                                <span class="reply-badge"><i class="fas fa-reply"></i> Replied</span>
                            <?php } ?>
                        </div>
                        <div class="message-meta">From: <?php echo safeHtml($msg['name']); ?> | <?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></div>
                        <div class="message-meta"><?php echo safeHtml(substr($msg['message'], 0, 100)); ?>...</div>
                        <?php if ($msg['reply']) { ?>
                            <div style="margin-top:10px; padding:10px; background:#d1fae5; border-radius:8px; border-left: 3px solid #28a745;">
                                <small style="color:#065f46; font-weight:600;"><i class="fas fa-reply"></i> Admin Reply:</small>
                                <p style="color:#065f46; margin-top:5px;"><?php echo safeHtml(substr($msg['reply'], 0, 150)); ?>...</p>
                                <small style="color:#64748b;">Replied on: <?php echo date('M d, H:i', strtotime($msg['replied_at'])); ?></small>
                            </div>
                        <?php } ?>
                        <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                            <button class="btn btn-small" onclick="viewFullMessage(<?php echo htmlspecialchars(json_encode($msg)); ?>)">View Full</button>
                            <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this message?')">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="delete_message" class="btn btn-small btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php } } 
                    
                    if ($inbox && $inbox->num_rows > 0) {
                        while ($msg = $inbox->fetch_assoc()) {
                            $has_messages = true;
                    ?>
                    <div class="message-card <?php echo !$msg['is_read'] ? 'message-unread' : ''; ?>">
                        <div class="message-subject"><?php echo safeHtml($msg['subject']); ?></div>
                        <div class="message-meta">From: <?php echo safeHtml($msg['sender_name']); ?> | <?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></div>
                        <div class="message-meta"><?php echo safeHtml(substr($msg['message'], 0, 100)); ?>...</div>
                        <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                            <button class="btn btn-small" onclick="viewFullMessage(<?php echo htmlspecialchars(json_encode($msg)); ?>)">View Full</button>
                            <form method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this message?')">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="delete_message" class="btn btn-small btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php } } 
                    
                    if (!$has_messages) { ?>
                    <p style="color:#64748b;">No messages yet.</p>
                    <?php } ?>
                </div>
                <div class="form-card">
                    <h3>Send Message</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>To</label>
                            <select name="receiver_id" required>
                                <option value="">Select User</option>
                                <?php if ($users && $users->num_rows > 0) {
                                    while ($u = $users->fetch_assoc()) { ?>
                                    <option value="<?php echo $u['id']; ?>"><?php echo safeHtml($u['username']); ?> <?php echo $u['role'] == 'admin' ? '(Admin)' : ''; ?></option>
                                    <?php } } ?>
                            </select>
                        </div>
                        <div class="form-group"><label>Subject</label><input type="text" name="subject" required></div>
                        <div class="form-group"><label>Message</label><textarea name="message" rows="4" required></textarea></div>
                        <button type="submit" name="send_message" class="btn" style="width:100%;"><i class="fas fa-paper-plane"></i> Send</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <div id="messageViewModal" class="message-view-modal">
        <div class="message-view-content">
            <span class="message-view-close" onclick="document.getElementById('messageViewModal').style.display='none'">&times;</span>
            <h2 id="viewSubject" style="margin-bottom:15px; color:#0a1628;">Message Subject</h2>
            <div style="background:#f8fafc; padding:15px; border-radius:10px; margin-bottom:15px;">
                <p><strong>From:</strong> <span id="viewName"></span></p>
                <p><strong>Email:</strong> <span id="viewEmail"></span></p>
                <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
                <p><strong>Date:</strong> <span id="viewDate"></span></p>
            </div>
            <hr style="margin:15px 0;">
            <h4 style="color:#0a1628;">Message:</h4>
            <p id="viewMessage" style="white-space:pre-wrap; line-height:1.8; background:white; padding:15px; border-radius:10px; margin-top:10px; border:1px solid #e2e8f0;"></p>
            <div id="viewReplySection" style="margin-top:15px; display:none;">
                <hr style="margin:15px 0;">
                <h4 style="color:#28a745;"><i class="fas fa-reply"></i> Reply:</h4>
                <p id="viewReplyText" style="white-space:pre-wrap; line-height:1.8; background:#d1fae5; padding:15px; border-radius:10px; margin-top:10px;"></p>
                <p style="color:#64748b; font-size:13px; margin-top:5px;">Replied on: <span id="viewReplyDate"></span></p>
            </div>
            <div style="margin-top:20px;">
                <button onclick="document.getElementById('messageViewModal').style.display='none'" class="btn btn-small">Close</button>
            </div>
        </div>
    </div>
    
    <script>
    function viewFullMessage(data) {
        document.getElementById('viewSubject').textContent = data.subject || '';
        document.getElementById('viewName').textContent = data.name || '';
        document.getElementById('viewEmail').textContent = data.email || '';
        document.getElementById('viewPhone').textContent = data.phone || 'N/A';
        document.getElementById('viewDate').textContent = data.created_at ? new Date(data.created_at).toLocaleString() : '';
        document.getElementById('viewMessage').textContent = data.message || '';
        
        if (data.reply) {
            document.getElementById('viewReplySection').style.display = 'block';
            document.getElementById('viewReplyText').textContent = data.reply || '';
            document.getElementById('viewReplyDate').textContent = data.replied_at ? new Date(data.replied_at).toLocaleString() : '';
        } else {
            document.getElementById('viewReplySection').style.display = 'none';
        }
        
        document.getElementById('messageViewModal').style.display = 'flex';
    }
    
    document.getElementById('messageViewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
    </script>
    
<?php
// ============================================
// 404 PAGE
// ============================================
} else { ?>
    <div style="text-align:center; padding:100px 0;">
        <i class="fas fa-car" style="font-size:80px; color:#cbd5e1; margin-bottom:30px;"></i>
        <h1 style="font-size:72px; color:#1e293b;">404</h1>
        <h2 style="font-family:'Playfair Display',serif; font-size:32px;">Page Not Found</h2>
        <a href="?page=home" class="btn" style="margin-top:20px;"><i class="fas fa-home"></i> Go Home</a>
    </div>
<?php } ?>

</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <?php
        $social = $conn ? $conn->query("SELECT * FROM social_media ORDER BY display_order") : null;
        $site_name = $conn ? getSetting('site_name', $conn) : 'GMotors';
        $address = $conn ? getSetting('contact_address', $conn) : 'Mwenge Mpakani, Dar es Salaam, Tanzania';
        $phone = $conn ? getSetting('contact_phone', $conn) : '+255 700 000 000';
        $email = $conn ? getSetting('contact_email', $conn) : 'info@gmotors.com';
        ?>
        <div class="footer-content">
            <div class="footer-brand">
                <h3><i class="fas fa-car"></i> <?php echo safeHtml($site_name); ?></h3>
                <p>Tanzania's premier online car marketplace. Buy and sell quality cars with confidence.</p>
                <div class="footer-social">
                    <?php if ($social && $social->num_rows > 0) { 
                        while ($s = $social->fetch_assoc()) { 
                            $bg = '#333';
                            $plat = strtolower($s['platform']);
                            if ($plat == 'facebook') $bg = '#1877f2';
                            elseif ($plat == 'instagram') $bg = '#d62976';
                            elseif ($plat == 'tiktok') $bg = '#000000';
                            elseif ($plat == 'youtube') $bg = '#ff0000';
                            elseif ($plat == 'whatsapp') $bg = '#25d366';
                            elseif ($plat == 'twitter') $bg = '#1da1f2';
                    ?>
                    <a href="<?php echo safeHtml($s['url'] ?? '#'); ?>" target="_blank" style="background: <?php echo $bg; ?>;">
                        <i class="<?php echo safeHtml($s['icon'] ?? 'fas fa-link'); ?>"></i>
                    </a>
                    <?php } } ?>
                </div>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="?page=home">Home</a></li>
                    <li><a href="?page=cars">Browse Cars</a></li>
                    <li><a href="?page=about">About Us</a></li>
                    <li><a href="?page=sell">Sell Your Car</a></li>
                    <li><a href="?page=contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-hours">
                <h4>Opening Hours</h4>
                <div class="hours-item"><span>Mon - Fri</span><span class="time">9am - 8pm</span></div>
                <div class="hours-item"><span>Saturday</span><span class="time">10am - 6pm</span></div>
                <div class="hours-item"><span>Sunday</span><span class="time">Closed</span></div>
            </div>
            <div class="footer-contact">
                <h4>Contact Info</h4>
                <div class="contact-item"><i class="fas fa-map-marker-alt"></i><div><?php echo safeHtml($address); ?></div></div>
                <div class="contact-item"><i class="fas fa-phone"></i><div><a href="tel:<?php echo safeHtml($phone); ?>" style="color:#b8c5d6;"><?php echo safeHtml($phone); ?></a></div></div>
                <div class="contact-item"><i class="fas fa-envelope"></i><div><a href="mailto:<?php echo safeHtml($email); ?>" style="color:#b8c5d6;"><?php echo safeHtml($email); ?></a></div></div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="copyright">&copy; <?php echo date('Y'); ?> <?php echo safeHtml($site_name); ?>. All rights reserved.</div>
            <div style="display:flex; gap:20px;">
                <a href="#" style="color:#b8c5d6; text-decoration:none; font-size:14px;">Privacy Policy</a>
                <a href="#" style="color:#b8c5d6; text-decoration:none; font-size:14px;">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script>
function toggleMobileNav() {
    document.getElementById('mobileNav').classList.toggle('active');
}

document.querySelectorAll('.toast').forEach(toast => {
    setTimeout(() => {
        toast.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => toast.style.display = 'none', 300);
    }, 5000);
});
</script>

<?php ob_end_flush(); ?>
</body>
</html>