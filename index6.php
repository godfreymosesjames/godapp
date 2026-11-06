<?php
// =============================================
// HOSTELPRO - Complete System with Public Landing Page
// =============================================

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Change this to your MySQL password
$db_name = 'hostelpro';

// =============================================
// AUTO DATABASE SETUP
// =============================================
function setupDatabase($host, $user, $pass, $dbname) {
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");
        
        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS rooms (
                id INT PRIMARY KEY AUTO_INCREMENT,
                room_number VARCHAR(10) UNIQUE NOT NULL,
                room_type VARCHAR(50) NOT NULL,
                capacity INT NOT NULL,
                status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
                guest_id INT NULL,
                price DECIMAL(10,2) DEFAULT 0,
                image_url VARCHAR(255) NULL,
                description TEXT NULL,
                facilities TEXT NULL
            )",
            
            "CREATE TABLE IF NOT EXISTS guests (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                room_id INT NOT NULL,
                check_in DATE NOT NULL,
                check_out DATE NULL,
                phone VARCHAR(20) NULL,
                email VARCHAR(100) NULL,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS payments (
                id INT PRIMARY KEY AUTO_INCREMENT,
                guest_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                payment_date DATE NOT NULL,
                status ENUM('paid', 'pending', 'overdue') DEFAULT 'pending',
                description VARCHAR(255) NULL,
                FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS hostel_images (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(100) NOT NULL,
                image_url VARCHAR(255) NOT NULL,
                category ENUM('room', 'facility', 'environment', 'common') DEFAULT 'room',
                description TEXT NULL,
                display_order INT DEFAULT 0
            )",
            
            "CREATE TABLE IF NOT EXISTS bookings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                guest_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                room_id INT NOT NULL,
                check_in DATE NOT NULL,
                check_out DATE NOT NULL,
                status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
                message TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
            )"
        ];
        
        foreach ($tables as $sql) {
            $pdo->exec($sql);
        }
        
        // Check if admin exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['admin', 'admin123', 'Administrator', 'admin']);
        }
        
        // Check if sample rooms exist
        $stmt = $pdo->prepare("SELECT id FROM rooms LIMIT 1");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $sampleRooms = [
                ['101', 'Single Room', 1, 'available', 50.00, 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=400', 'Cozy single room with study desk', 'Desk, Chair, Wardrobe, WiFi'],
                ['102', 'Double Room', 2, 'available', 80.00, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?w=400', 'Spacious double room with en-suite', 'Double Bed, En-suite, TV, WiFi'],
                ['103', 'Dorm Room', 4, 'available', 30.00, 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400', 'Modern dorm with 4 beds', 'Bunk Beds, Lockers, Study Area'],
                ['104', 'Suite', 3, 'available', 120.00, 'https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?w=400', 'Luxury suite with living area', 'King Bed, Living Room, Kitchenette'],
                ['105', 'Double Room', 2, 'occupied', 80.00, 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=400', 'Comfortable double room', 'Double Bed, Desk, WiFi']
            ];
            $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, capacity, status, price, image_url, description, facilities) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($sampleRooms as $room) {
                $stmt->execute($room);
            }
        }
        
        // Check if sample images exist
        $stmt = $pdo->prepare("SELECT id FROM hostel_images LIMIT 1");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $sampleImages = [
                ['Main Building', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600', 'facility', 'Modern hostel building with garden'],
                ['Common Lounge', 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600', 'common', 'Comfortable common area for socializing'],
                ['Garden Area', 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600', 'environment', 'Beautiful garden with seating'],
                ['Dining Area', 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=600', 'facility', 'Spacious dining hall'],
                ['Study Room', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600', 'facility', 'Quiet study area with computers'],
                ['Gym', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600', 'facility', 'Fully equipped gym']
            ];
            $stmt = $pdo->prepare("INSERT INTO hostel_images (title, image_url, category, description) VALUES (?, ?, ?, ?)");
            foreach ($sampleImages as $img) {
                $stmt->execute($img);
            }
        }
        
        return $pdo;
    } catch(PDOException $e) {
        die("Database setup failed: " . $e->getMessage());
    }
}

$pdo = setupDatabase($db_host, $db_user, $db_pass, $db_name);

// =============================================
// API ROUTES
// =============================================
$action = isset($_GET['action']) ? $_GET['action'] : '';
$method = $_SERVER['REQUEST_METHOD'];

if (strpos($_SERVER['REQUEST_URI'], '?action=') !== false || $action) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
    function authenticate($pdo) {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        if (empty($authHeader)) return null;
        $token = str_replace('Bearer ', '', $authHeader);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    function getRequestData() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    // --- PUBLIC ROUTES ---
    if ($method === 'GET' && $action === 'public_rooms') {
        $stmt = $pdo->query("SELECT id, room_number, room_type, capacity, status, price, image_url, description, facilities FROM rooms WHERE status = 'available' ORDER BY id");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    
    if ($method === 'GET' && $action === 'public_images') {
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $sql = "SELECT * FROM hostel_images ORDER BY display_order";
        if ($category) {
            $sql = "SELECT * FROM hostel_images WHERE category = ? ORDER BY display_order";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query($sql);
        }
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    
    if ($method === 'POST' && $action === 'public_booking') {
        $data = getRequestData();
        $stmt = $pdo->prepare("INSERT INTO bookings (guest_name, email, phone, room_id, check_in, check_out, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['guest_name'], $data['email'], $data['phone'], $data['room_id'], $data['check_in'], $data['check_out'], $data['message'] ?? '']);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit();
    }
    
    // --- LOGIN ---
    if ($method === 'POST' && $action === 'login') {
        $data = getRequestData();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$data['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['password'] === $data['password']) {
            unset($user['password']);
            echo json_encode(['success' => true, 'user' => $user, 'token' => $user['id']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
        }
        exit();
    }
    
    // --- PROTECTED ROUTES ---
    $user = authenticate($pdo);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
    
    // --- GET ROOMS ---
    if ($method === 'GET' && $action === 'rooms') {
        $stmt = $pdo->query("SELECT r.*, g.name as guest_name FROM rooms r LEFT JOIN guests g ON r.guest_id = g.id ORDER BY r.id");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    
    // --- GET GUESTS ---
    if ($method === 'GET' && $action === 'guests') {
        $stmt = $pdo->query("SELECT g.*, r.room_number FROM guests g JOIN rooms r ON g.room_id = r.id ORDER BY g.id");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    
    // --- GET BOOKINGS ---
    if ($method === 'GET' && $action === 'bookings') {
        $stmt = $pdo->query("SELECT b.*, r.room_number FROM bookings b JOIN rooms r ON b.room_id = r.id ORDER BY b.created_at DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    
    // --- GET PAYMENTS ---
    if ($method === 'GET' && $action === 'payments') {
        $guestId = isset($_GET['guest_id']) ? $_GET['guest_id'] : 0;
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE guest_id = ? ORDER BY payment_date DESC");
        $stmt->execute([$guestId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    
    // --- GET IMAGES ---
    if ($method === 'GET' && $action === 'images') {
        $stmt = $pdo->query("SELECT * FROM hostel_images ORDER BY category, display_order");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit();
    }
    
    // --- GET STATS ---
    if ($method === 'GET' && $action === 'stats') {
        $stats = [];
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM rooms");
        $stats['totalRooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM rooms WHERE status = 'occupied'");
        $stats['occupied'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM rooms WHERE status = 'available'");
        $stats['available'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM guests");
        $stats['guests'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'");
        $stats['pendingBookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo json_encode($stats);
        exit();
    }
    
    // --- ADD ROOM ---
    if ($method === 'POST' && $action === 'room') {
        $data = getRequestData();
        $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, capacity, status, price, image_url, description, facilities) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['room_number'], $data['room_type'], $data['capacity'], $data['status'], $data['price'] ?? 0, $data['image_url'] ?? '', $data['description'] ?? '', $data['facilities'] ?? '']);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit();
    }
    
    // --- UPDATE ROOM ---
    if ($method === 'PUT' && $action === 'room') {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $data = getRequestData();
        $stmt = $pdo->prepare("UPDATE rooms SET room_number=?, room_type=?, capacity=?, status=?, price=?, image_url=?, description=?, facilities=? WHERE id=?");
        $stmt->execute([$data['room_number'], $data['room_type'], $data['capacity'], $data['status'], $data['price'] ?? 0, $data['image_url'] ?? '', $data['description'] ?? '', $data['facilities'] ?? '', $id]);
        echo json_encode(['success' => true]);
        exit();
    }
    
    // --- DELETE ROOM ---
    if ($method === 'DELETE' && $action === 'room') {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM guests WHERE room_id = ?");
        $stmt->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        $pdo->commit();
        echo json_encode(['success' => true]);
        exit();
    }
    
    // --- ADD IMAGE ---
    if ($method === 'POST' && $action === 'image') {
        $data = getRequestData();
        $stmt = $pdo->prepare("INSERT INTO hostel_images (title, image_url, category, description, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['title'], $data['image_url'], $data['category'], $data['description'] ?? '', $data['display_order'] ?? 0]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit();
    }
    
    // --- DELETE IMAGE ---
    if ($method === 'DELETE' && $action === 'image') {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $stmt = $pdo->prepare("DELETE FROM hostel_images WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit();
    }
    
    // --- UPDATE BOOKING STATUS ---
    if ($method === 'PUT' && $action === 'booking') {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $data = getRequestData();
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $id]);
        echo json_encode(['success' => true]);
        exit();
    }
    
    // --- CHECKIN ---
    if ($method === 'POST' && $action === 'checkin') {
        $data = getRequestData();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO guests (name, room_id, check_in, phone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['room_id'], $data['check_in'] ?? date('Y-m-d'), $data['phone'] ?? '', $data['email'] ?? '']);
        $guestId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("UPDATE rooms SET status='occupied', guest_id=? WHERE id=?");
        $stmt->execute([$guestId, $data['room_id']]);
        $pdo->commit();
        echo json_encode(['success' => true, 'guest_id' => $guestId]);
        exit();
    }
    
    // --- CHECKOUT ---
    if ($method === 'POST' && $action === 'checkout') {
        $roomId = isset($_GET['room_id']) ? $_GET['room_id'] : 0;
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM guests WHERE room_id = ?");
        $stmt->execute([$roomId]);
        $stmt = $pdo->prepare("UPDATE rooms SET status='available', guest_id=NULL WHERE id=?");
        $stmt->execute([$roomId]);
        $pdo->commit();
        echo json_encode(['success' => true]);
        exit();
    }
    
    // --- DELETE GUEST ---
    if ($method === 'DELETE' && $action === 'guest') {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT room_id FROM guests WHERE id = ?");
        $stmt->execute([$id]);
        $guest = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($guest) {
            $stmt = $pdo->prepare("UPDATE rooms SET status='available', guest_id=NULL WHERE id=?");
            $stmt->execute([$guest['room_id']]);
        }
        $stmt = $pdo->prepare("DELETE FROM guests WHERE id = ?");
        $stmt->execute([$id]);
        $pdo->commit();
        echo json_encode(['success' => true]);
        exit();
    }
    
    // --- ADD PAYMENT ---
    if ($method === 'POST' && $action === 'payment') {
        $data = getRequestData();
        $stmt = $pdo->prepare("INSERT INTO payments (guest_id, amount, payment_date, status, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['guest_id'], $data['amount'], $data['payment_date'] ?? date('Y-m-d'), $data['status'] ?? 'paid', $data['description'] ?? '']);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit();
    }
    
    // --- REGISTER USER ---
    if ($method === 'POST' && $action === 'register') {
        $data = getRequestData();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Username already exists']);
            exit();
        }
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['username'], $data['password'], $data['full_name'], $data['role'] ?? 'user']);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit();
    }
    
    echo json_encode(['error' => 'Invalid endpoint']);
    exit();
}

// =============================================
// HTML PAGE STARTS HERE
// =============================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HostelPro · Complete Hostel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
        }

        body {
            background: #f0f4fb;
            min-height: 100vh;
        }

        /* ========== NAVIGATION ========== */
        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .navbar .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0b2b4a;
        }
        .navbar .logo i {
            color: #1a5fb0;
            margin-right: 8px;
        }
        .navbar .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .navbar .nav-links a {
            text-decoration: none;
            color: #4c6f96;
            font-weight: 500;
            transition: 0.15s;
            cursor: pointer;
        }
        .navbar .nav-links a:hover {
            color: #1a5fb0;
        }
        .navbar .nav-links .btn {
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.15s;
        }
        .btn-primary {
            background: #1a5fb0;
            color: white;
        }
        .btn-primary:hover {
            background: #134b8f;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #1d3755;
        }
        .btn-outline:hover {
            background: #e6edf8;
        }
        .btn-success {
            background: #0f7b3a;
            color: white;
        }
        .btn-success:hover {
            background: #0b632e;
        }
        .btn-danger {
            background: #b13e3e;
            color: white;
        }
        .btn-danger:hover {
            background: #932f2f;
        }

        /* ========== HERO SECTION ========== */
        .hero {
            background: linear-gradient(135deg, #0b2b4a 0%, #1a5fb0 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .hero .btn {
            padding: 0.8rem 2.5rem;
            font-size: 1.1rem;
        }

        /* ========== SECTIONS ========== */
        .section {
            padding: 3rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #0b2b4a;
            margin-bottom: 2rem;
            text-align: center;
        }
        .section-title i {
            color: #1a5fb0;
            margin-right: 10px;
        }

        /* ========== ROOM CARDS ========== */
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        .room-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .room-card:hover {
            transform: translateY(-4px);
        }
        .room-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .room-card .content {
            padding: 1.5rem;
        }
        .room-card .content h3 {
            color: #0b2b4a;
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
        }
        .room-card .content .room-type {
            color: #4c6f96;
            font-size: 0.9rem;
        }
        .room-card .content .price {
            color: #1a5fb0;
            font-weight: 700;
            font-size: 1.3rem;
            margin: 0.5rem 0;
        }
        .room-card .content .facilities {
            color: #4c6f96;
            font-size: 0.85rem;
            margin: 0.5rem 0;
        }
        .room-card .content .facilities i {
            color: #1a5fb0;
            margin-right: 4px;
        }
        .room-card .content .btn {
            width: 100%;
            margin-top: 0.5rem;
        }

        /* ========== IMAGE GALLERY ========== */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .gallery-item {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            height: 250px;
            cursor: pointer;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        .gallery-item .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5rem;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
        }
        .gallery-item .overlay h4 {
            font-size: 1.1rem;
        }
        .gallery-item .overlay p {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* ========== BOOKING FORM ========== */
        .booking-form {
            background: white;
            padding: 2rem;
            border-radius: 24px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .booking-form .form-group {
            margin-bottom: 1.2rem;
        }
        .booking-form label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1d3755;
            display: block;
            margin-bottom: 4px;
        }
        .booking-form input, .booking-form select, .booking-form textarea {
            width: 100%;
            padding: 0.7rem 1.2rem;
            border: 1px solid #d7e0ea;
            border-radius: 40px;
            font-size: 0.95rem;
        }
        .booking-form textarea {
            border-radius: 20px;
            min-height: 80px;
            resize: vertical;
        }
        .booking-form input:focus, .booking-form select:focus, .booking-form textarea:focus {
            outline: none;
            border-color: #1a5fb0;
            box-shadow: 0 0 0 4px rgba(26,95,176,0.1);
        }

        /* ========== DASHBOARD ========== */
        .dashboard {
            display: none;
        }
        .dashboard.active {
            display: block;
        }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .dash-header .left h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0b2b4a;
        }
        .dash-header .left i {
            color: #1a5fb0;
            margin-right: 10px;
        }
        .dash-header .right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .user-badge {
            background: #e9f0fe;
            padding: 0.4rem 1.2rem 0.4rem 1rem;
            border-radius: 60px;
            font-weight: 600;
            color: #0b2b4a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-badge i {
            color: #1a5fb0;
        }

        .stat-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #f8faff;
            padding: 0.8rem 1.8rem 0.8rem 1.5rem;
            border-radius: 60px;
            border: 1px solid #e3ebf5;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stat-card i {
            font-size: 1.3rem;
            color: #1a5fb0;
            width: 2rem;
        }
        .stat-card .info .label {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #4c6f96;
        }
        .stat-card .info .value {
            font-weight: 700;
            font-size: 1.3rem;
            color: #0b2b4a;
        }

        .admin-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            background: #f2f6fe;
            padding: 0.7rem 1.8rem;
            border-radius: 60px;
            margin-bottom: 2rem;
            gap: 0.8rem;
        }
        .admin-bar .btn-group {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            padding: 0.45rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: 0.15s;
            border: 1px solid transparent;
            color: #1d3755;
            text-decoration: none;
        }
        .btn-sm {
            padding: 0.3rem 0.8rem;
            font-size: 0.75rem;
        }
        .btn-primary {
            background: #1a5fb0;
            color: white;
        }
        .btn-primary:hover {
            background: #134b8f;
        }
        .btn-outline {
            background: transparent;
            border-color: #cbd5e1;
        }
        .btn-outline:hover {
            background: #e6edf8;
        }
        .btn-success {
            background: #0f7b3a;
            color: white;
        }
        .btn-success:hover {
            background: #0b632e;
        }
        .btn-danger {
            background: #b13e3e;
            color: white;
        }
        .btn-danger:hover {
            background: #932f2f;
        }
        .btn-warning {
            background: #b8860b;
            color: white;
        }
        .btn-warning:hover {
            background: #9e740a;
        }

        .table-wrap {
            background: white;
            border-radius: 24px;
            border: 1px solid #e6edf6;
            overflow-x: auto;
            margin-bottom: 2rem;
            box-shadow: 0 6px 16px -10px rgba(0,0,0,0.04);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 680px;
        }
        th {
            background: #f8fafd;
            text-align: left;
            padding: 0.9rem 1.2rem;
            font-weight: 600;
            color: #1f3b5c;
            border-bottom: 1px solid #dce3ed;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        td {
            padding: 0.8rem 1.2rem;
            border-bottom: 1px solid #ebf0f7;
            color: #1f334f;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background: #fafcff;
        }
        .badge {
            background: #e6edf8;
            padding: 0.2rem 0.9rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .badge.available {
            background: #d9f0e1;
            color: #0b5e2e;
        }
        .badge.occupied {
            background: #fce8e8;
            color: #a13232;
        }
        .badge.paid {
            background: #d4edda;
            color: #0b5e2e;
        }
        .badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge.confirmed {
            background: #d4e3ff;
            color: #144e8a;
        }
        .badge.cancelled {
            background: #fce8e8;
            color: #a13232;
        }
        .action-icons i {
            margin: 0 4px;
            color: #4a6e96;
            cursor: pointer;
            font-size: 0.95rem;
            transition: 0.1s;
        }
        .action-icons i:hover {
            color: #0b2b4a;
            transform: scale(1.05);
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.25);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-card {
            background: white;
            max-width: 600px;
            width: 92%;
            padding: 2.2rem 2.2rem 1.8rem;
            border-radius: 44px;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.4);
            animation: fadeUp 0.2s ease;
            max-height: 90vh;
            overflow-y: auto;
        }
        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .modal-card h2 {
            margin-bottom: 1.5rem;
            color: #0b2b4a;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .modal-card .form-group {
            margin-bottom: 1.2rem;
        }
        .modal-card label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1d3755;
            display: block;
            margin-bottom: 4px;
        }
        .modal-card input, .modal-card select, .modal-card textarea {
            width: 100%;
            padding: 0.7rem 1.2rem;
            border: 1px solid #d0dbea;
            border-radius: 40px;
            background: #fafcff;
            font-size: 0.95rem;
        }
        .modal-card textarea {
            border-radius: 20px;
            min-height: 80px;
            resize: vertical;
        }
        .modal-card input:focus, .modal-card select:focus, .modal-card textarea:focus {
            outline: none;
            border-color: #1a5fb0;
            box-shadow: 0 0 0 4px rgba(26,95,176,0.1);
        }
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 1.8rem;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
            gap: 1rem;
        }
        .loading-overlay.active {
            display: flex;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e9f0fe;
            border-top-color: #1a5fb0;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .login-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .login-overlay.active {
            display: flex;
        }
        .login-modal {
            background: white;
            max-width: 420px;
            width: 92%;
            padding: 2.5rem;
            border-radius: 48px;
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.5);
            animation: fadeUp 0.3s ease;
        }
        .login-modal .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-modal .brand i {
            font-size: 2.8rem;
            color: #1a5fb0;
            background: #e9f0fe;
            padding: 0.7rem;
            border-radius: 30px;
        }
        .login-modal .brand h2 {
            margin-top: 0.6rem;
            font-weight: 700;
            color: #0b2b4a;
        }
        .login-modal .form-group {
            margin-bottom: 1.2rem;
        }
        .login-modal label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1d3755;
            display: block;
            margin-bottom: 4px;
        }
        .login-modal input {
            width: 100%;
            padding: 0.8rem 1.2rem;
            border: 1px solid #d7e0ea;
            border-radius: 40px;
            font-size: 0.95rem;
        }
        .login-modal input:focus {
            outline: none;
            border-color: #1a5fb0;
            box-shadow: 0 0 0 4px rgba(26,95,176,0.1);
        }
        .login-modal .login-error {
            color: #b13e3e;
            font-size: 0.8rem;
            text-align: center;
            background: #fce8e8;
            padding: 0.4rem;
            border-radius: 40px;
            display: none;
            margin-top: 0.8rem;
        }

        .section-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .section-tabs .tab-btn {
            background: white;
            border: 1px solid #d7e0ea;
            padding: 0.5rem 1.5rem;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            color: #4c6f96;
            transition: 0.15s;
        }
        .section-tabs .tab-btn:hover {
            background: #e6edf8;
        }
        .section-tabs .tab-btn.active {
            background: #1a5fb0;
            color: white;
            border-color: #1a5fb0;
        }

        .hidden {
            display: none !important;
        }

        .user-room-card {
            background: #f8faff;
            border-radius: 28px;
            padding: 1.8rem 2rem;
            border: 1px solid #e3ebf5;
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            align-items: center;
        }
        .user-room-card .detail {
            display: flex;
            flex-direction: column;
        }
        .user-room-card .detail .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #4c6f96;
        }
        .user-room-card .detail .value {
            font-weight: 700;
            font-size: 1.3rem;
            color: #0b2b4a;
        }

        @media (max-width: 768px) {
            .navbar { flex-direction: column; align-items: stretch; }
            .navbar .nav-links { justify-content: center; }
            .hero h1 { font-size: 2rem; }
            .section { padding: 2rem 1rem; }
            .room-grid { grid-template-columns: 1fr; }
            .gallery-grid { grid-template-columns: 1fr 1fr; }
            .admin-bar { flex-direction: column; align-items: stretch; }
            .dash-header { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 480px) {
            .gallery-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ========== LOADING OVERLAY ========== -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p style="color: #0b2b4a; font-weight: 600;">Loading...</p>
</div>

<!-- ========== LOGIN OVERLAY ========== -->
<div class="login-overlay" id="loginOverlay">
    <div class="login-modal">
        <div class="brand">
            <i class="fas fa-hotel"></i>
            <h2>HostelPro</h2>
            <p style="color: #4c6f96;">Login to manage your hostel</p>
        </div>
        <form id="loginForm">
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="loginUsername" placeholder="Enter username" value="admin" />
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="loginPassword" placeholder="Enter password" value="admin123" />
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding:0.9rem; font-size:1rem;">
                <i class="fas fa-sign-in-alt"></i> Log in
            </button>
            <div class="login-error" id="loginError">Invalid credentials</div>
        </form>
        <p style="text-align:center; margin-top:1.5rem; font-size:0.75rem; color:#6b85a3;">
            Default: admin / admin123
        </p>
    </div>
</div>

<!-- ========== NAVIGATION ========== -->
<nav class="navbar">
    <div class="logo"><i class="fas fa-hotel"></i> HostelPro</div>
    <div class="nav-links">
        <a onclick="showSection('home')"><i class="fas fa-home"></i> Home</a>
        <a onclick="showSection('rooms')"><i class="fas fa-door-open"></i> Rooms</a>
        <a onclick="showSection('gallery')"><i class="fas fa-images"></i> Gallery</a>
        <a onclick="showSection('booking')"><i class="fas fa-calendar-check"></i> Book Now</a>
        <button class="btn btn-primary" id="loginBtn" onclick="openLogin()"><i class="fas fa-user"></i> Login</button>
        <button class="btn btn-outline hidden" id="dashboardBtn" onclick="showDashboard()"><i class="fas fa-tachometer-alt"></i> Dashboard</button>
        <button class="btn btn-danger hidden" id="logoutBtn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
</nav>

<!-- ========== HERO ========== -->
<section class="hero" id="homeSection">
    <h1>Welcome to HostelPro</h1>
    <p>Your comfortable home away from home. Modern rooms, great facilities, and a friendly community.</p>
    <button class="btn btn-primary" onclick="showSection('rooms')"><i class="fas fa-search"></i> View Rooms</button>
    <button class="btn btn-outline" style="background:rgba(255,255,255,0.15); color:white; border-color:rgba(255,255,255,0.3);" onclick="showSection('booking')"><i class="fas fa-calendar-plus"></i> Book Now</button>
</section>

<!-- ========== ROOMS SECTION ========== -->
<section class="section" id="roomsSection">
    <h2 class="section-title"><i class="fas fa-door-open"></i> Our Rooms</h2>
    <div class="room-grid" id="roomGrid">
        <!-- Rooms loaded dynamically -->
    </div>
</section>

<!-- ========== GALLERY SECTION ========== -->
<section class="section" id="gallerySection">
    <h2 class="section-title"><i class="fas fa-images"></i> Gallery</h2>
    <div class="section-tabs">
        <button class="tab-btn active" onclick="filterGallery('all')">All</button>
        <button class="tab-btn" onclick="filterGallery('room')">Rooms</button>
        <button class="tab-btn" onclick="filterGallery('facility')">Facilities</button>
        <button class="tab-btn" onclick="filterGallery('environment')">Environment</button>
        <button class="tab-btn" onclick="filterGallery('common')">Common Areas</button>
    </div>
    <div class="gallery-grid" id="galleryGrid">
        <!-- Gallery loaded dynamically -->
    </div>
</section>

<!-- ========== BOOKING SECTION ========== -->
<section class="section" id="bookingSection">
    <h2 class="section-title"><i class="fas fa-calendar-check"></i> Book a Room</h2>
    <form class="booking-form" id="bookingForm">
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" id="bookName" placeholder="Your full name" required />
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" id="bookEmail" placeholder="your@email.com" required />
        </div>
        <div class="form-group">
            <label>Phone *</label>
            <input type="tel" id="bookPhone" placeholder="+1234567890" required />
        </div>
        <div class="form-group">
            <label>Select Room *</label>
            <select id="bookRoom" required>
                <option value="">Choose a room...</option>
            </select>
        </div>
        <div class="form-group">
            <label>Check-in Date *</label>
            <input type="date" id="bookCheckIn" required />
        </div>
        <div class="form-group">
            <label>Check-out Date *</label>
            <input type="date" id="bookCheckOut" required />
        </div>
        <div class="form-group">
            <label>Special Requests</label>
            <textarea id="bookMessage" placeholder="Any special requirements..."></textarea>
        </div>
        <button type="submit" class="btn btn-success" style="width:100%; padding:0.9rem; font-size:1rem;">
            <i class="fas fa-paper-plane"></i> Submit Booking
        </button>
    </form>
</section>

<!-- ========== DASHBOARD ========== -->
<div class="section" id="dashboardSection">
    <div class="dashboard active" id="dashboard">
        <div class="dash-header">
            <div class="left">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            </div>
            <div class="right">
                <div class="user-badge"><i class="fas fa-user-circle"></i> <span id="currentUserDisplay">Admin</span></div>
                <button class="btn btn-outline" onclick="hideDashboard()"><i class="fas fa-arrow-left"></i> Back to Site</button>
            </div>
        </div>

        <div class="stat-grid" id="statsGrid">
            <div class="stat-card"><i class="fas fa-door-open"></i><div class="info"><span class="label">Rooms</span><span class="value" id="totalRooms">0</span></div></div>
            <div class="stat-card"><i class="fas fa-user-graduate"></i><div class="info"><span class="label">Occupied</span><span class="value" id="occupiedCount">0</span></div></div>
            <div class="stat-card"><i class="fas fa-user-plus"></i><div class="info"><span class="label">Available</span><span class="value" id="availableCount">0</span></div></div>
            <div class="stat-card"><i class="fas fa-users"></i><div class="info"><span class="label">Guests</span><span class="value" id="guestCount">0</span></div></div>
            <div class="stat-card"><i class="fas fa-clock"></i><div class="info"><span class="label">Pending Bookings</span><span class="value" id="pendingBookings">0</span></div></div>
        </div>

        <div class="admin-bar">
            <div class="btn-group">
                <button class="btn btn-primary" id="addRoomBtn"><i class="fas fa-plus-circle"></i> Add Room</button>
                <button class="btn btn-success" id="checkinBtn"><i class="fas fa-user-plus"></i> Check-in</button>
                <button class="btn btn-warning" id="registerUserBtn"><i class="fas fa-user-cog"></i> Register User</button>
                <button class="btn btn-outline" id="addImageBtn"><i class="fas fa-image"></i> Add Image</button>
                <button class="btn btn-outline" id="addPaymentBtn"><i class="fas fa-coins"></i> Add Payment</button>
            </div>
            <div class="btn-group">
                <button class="btn btn-outline" id="refreshBtn"><i class="fas fa-sync-alt"></i> Refresh</button>
            </div>
        </div>

        <!-- Rooms Table -->
        <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-list-ul" style="color:#1a5fb0;"></i> Room Inventory</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Room</th><th>Type</th><th>Capacity</th><th>Price</th><th>Status</th><th>Guest</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody id="roomTableBody"></tbody>
            </table>
        </div>

        <!-- Guests Table -->
        <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-users" style="color:#1a5fb0;"></i> Current Guests</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Guest</th><th>Room</th><th>Check-in</th><th>Contact</th><th>Payments</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody id="guestTableBody"></tbody>
            </table>
        </div>

        <!-- Bookings Table -->
        <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-calendar-check" style="color:#1a5fb0;"></i> Bookings</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody id="bookingTableBody"></tbody>
            </table>
        </div>

        <!-- Gallery Management -->
        <h3 style="color:#0b2b4a; margin:1.8rem 0 0.6rem;"><i class="fas fa-images" style="color:#1a5fb0;"></i> Gallery Management</h3>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Title</th><th>Category</th><th>Image</th><th style="text-align:right;">Actions</th></tr></thead>
                <tbody id="imageTableBody"></tbody>
            </table>
        </div>

        <div style="margin-top:1rem; font-size:0.75rem; color:#7b90ab; text-align:right;">
            <i class="fas fa-database"></i> MySQL · Auto-created
        </div>
    </div>
</div>

<!-- ========== MODALS ========== -->
<div class="modal-overlay" id="roomModal">
    <div class="modal-card">
        <h2 id="roomModalTitle"><i class="fas fa-door-open" style="color:#1a5fb0;"></i> Add Room</h2>
        <form id="roomForm">
            <div class="form-group"><label>Room Number *</label><input type="text" id="roomNumber" placeholder="101" required /></div>
            <div class="form-group"><label>Room Type *</label>
                <select id="roomType"><option value="Single">Single</option><option value="Double">Double</option><option value="Dorm">Dorm</option><option value="Suite">Suite</option></select>
            </div>
            <div class="form-group"><label>Capacity *</label><input type="number" id="roomCapacity" value="1" min="1" required /></div>
            <div class="form-group"><label>Price per night ($)</label><input type="number" id="roomPrice" value="50" step="0.01" /></div>
            <div class="form-group"><label>Image URL</label><input type="text" id="roomImage" placeholder="https://example.com/image.jpg" /></div>
            <div class="form-group"><label>Description</label><textarea id="roomDescription" placeholder="Room description..."></textarea></div>
            <div class="form-group"><label>Facilities</label><input type="text" id="roomFacilities" placeholder="WiFi, TV, Desk, etc." /></div>
            <div class="form-group"><label>Status</label>
                <select id="roomStatus"><option value="available">Available</option><option value="occupied">Occupied</option><option value="maintenance">Maintenance</option></select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="roomModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Room</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="imageModal">
    <div class="modal-card">
        <h2><i class="fas fa-image" style="color:#1a5fb0;"></i> Add Image</h2>
        <form id="imageForm">
            <div class="form-group"><label>Title *</label><input type="text" id="imgTitle" placeholder="Beautiful Garden" required /></div>
            <div class="form-group"><label>Image URL *</label><input type="text" id="imgUrl" placeholder="https://example.com/image.jpg" required /></div>
            <div class="form-group"><label>Category</label>
                <select id="imgCategory"><option value="room">Room</option><option value="facility">Facility</option><option value="environment">Environment</option><option value="common">Common Area</option></select>
            </div>
            <div class="form-group"><label>Description</label><textarea id="imgDescription" placeholder="Image description..."></textarea></div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="imageModalCancel">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Image</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="registerModal">
    <div class="modal-card">
        <h2><i class="fas fa-user-plus" style="color:#1a5fb0;"></i> Register User</h2>
        <form id="registerForm">
            <div class="form-group"><label>Full Name *</label><input type="text" id="regName" placeholder="John Doe" required /></div>
            <div class="form-group"><label>Username *</label><input type="text" id="regUsername" placeholder="johndoe" required /></div>
            <div class="form-group"><label>Password *</label><input type="password" id="regPassword" placeholder="••••••" required /></div>
            <div class="form-group"><label>Role</label>
                <select id="regRole"><option value="user">User</option><option value="admin">Admin</option></select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="regCancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Register</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="paymentModal">
    <div class="modal-card">
        <h2><i class="fas fa-coins" style="color:#1a5fb0;"></i> Add Payment</h2>
        <form id="paymentForm">
            <div class="form-group"><label>Guest</label>
                <select id="paymentGuest"><option value="">Select guest</option></select>
            </div>
            <div class="form-group"><label>Amount ($) *</label><input type="number" id="paymentAmount" placeholder="0.00" step="0.01" required /></div>
            <div class="form-group"><label>Date *</label><input type="date" id="paymentDate" required /></div>
            <div class="form-group"><label>Status</label>
                <select id="paymentStatus"><option value="paid">Paid</option><option value="pending">Pending</option><option value="overdue">Overdue</option></select>
            </div>
            <div class="form-group"><label>Description</label><input type="text" id="paymentDescription" placeholder="Payment for..." /></div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="paymentCancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- ========== JAVASCRIPT ========== -->
<script>
// =============================================
// GLOBALS
// =============================================
const API_URL = window.location.pathname;
let currentUser = null;
let authToken = null;
let currentSection = 'home';
let currentGalleryFilter = 'all';

// DOM refs
const loadingOverlay = document.getElementById('loadingOverlay');
const loginOverlay = document.getElementById('loginOverlay');
const loginForm = document.getElementById('loginForm');
const loginError = document.getElementById('loginError');
const loginUsername = document.getElementById('loginUsername');
const loginPassword = document.getElementById('loginPassword');
const loginBtn = document.getElementById('loginBtn');
const logoutBtn = document.getElementById('logoutBtn');
const dashboardBtn = document.getElementById('dashboardBtn');
const roomGrid = document.getElementById('roomGrid');
const galleryGrid = document.getElementById('galleryGrid');
const bookingForm = document.getElementById('bookingForm');

// Dashboard refs
const currentUserDisplay = document.getElementById('currentUserDisplay');
const roomTableBody = document.getElementById('roomTableBody');
const guestTableBody = document.getElementById('guestTableBody');
const bookingTableBody = document.getElementById('bookingTableBody');
const imageTableBody = document.getElementById('imageTableBody');
const totalRoomsEl = document.getElementById('totalRooms');
const occupiedCountEl = document.getElementById('occupiedCount');
const availableCountEl = document.getElementById('availableCount');
const guestCountEl = document.getElementById('guestCount');
const pendingBookingsEl = document.getElementById('pendingBookings');

// Modal refs
const roomModal = document.getElementById('roomModal');
const roomForm = document.getElementById('roomForm');
const roomModalCancel = document.getElementById('roomModalCancel');
const imageModal = document.getElementById('imageModal');
const imageForm = document.getElementById('imageForm');
const imageModalCancel = document.getElementById('imageModalCancel');
const registerModal = document.getElementById('registerModal');
const registerForm = document.getElementById('registerForm');
const regCancelBtn = document.getElementById('regCancelBtn');
const paymentModal = document.getElementById('paymentModal');
const paymentForm = document.getElementById('paymentForm');
const paymentGuest = document.getElementById('paymentGuest');
const paymentCancelBtn = document.getElementById('paymentCancelBtn');

let editingRoomId = null;
let editingImageId = null;

// =============================================
// API HELPERS
// =============================================
async function apiRequest(action, method = 'GET', data = null) {
    const url = `${API_URL}?action=${action}`;
    const options = {
        method: method,
        headers: { 'Content-Type': 'application/json' }
    };
    if (authToken) {
        options.headers['Authorization'] = `Bearer ${authToken}`;
    }
    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { error: 'Connection failed' };
    }
}

// =============================================
// AUTH
// =============================================
async function login(username, password) {
    loadingOverlay.classList.add('active');
    try {
        const result = await apiRequest('login', 'POST', { username, password });
        if (result.success) {
            currentUser = result.user;
            authToken = result.token;
            loginOverlay.classList.remove('active');
            updateUIForAuth();
            if (currentUser.role === 'admin') {
                loadDashboardData();
            }
            loadingOverlay.classList.remove('active');
            showSection('home');
        } else {
            loginError.style.display = 'block';
            loginError.textContent = result.error || 'Invalid credentials';
            loadingOverlay.classList.remove('active');
        }
    } catch (error) {
        loginError.style.display = 'block';
        loginError.textContent = 'Connection error';
        loadingOverlay.classList.remove('active');
    }
}

function logout() {
    currentUser = null;
    authToken = null;
    updateUIForAuth();
    hideDashboard();
    showSection('home');
    loginOverlay.classList.add('active');
}

function updateUIForAuth() {
    if (currentUser) {
        loginBtn.classList.add('hidden');
        logoutBtn.classList.remove('hidden');
        dashboardBtn.classList.remove('hidden');
        currentUserDisplay.textContent = currentUser.full_name + (currentUser.role === 'admin' ? ' (Admin)' : '');
    } else {
        loginBtn.classList.remove('hidden');
        logoutBtn.classList.add('hidden');
        dashboardBtn.classList.add('hidden');
    }
}

function openLogin() {
    loginOverlay.classList.add('active');
    loginError.style.display = 'none';
}

// =============================================
// SECTION NAVIGATION
// =============================================
function showSection(section) {
    currentSection = section;
    document.querySelectorAll('.section').forEach(el => {
        if (el.id === section + 'Section') {
            el.style.display = 'block';
        } else if (el.id !== 'dashboardSection') {
            el.style.display = 'none';
        }
    });
    // Hide dashboard if showing other sections
    if (section !== 'dashboard') {
        document.getElementById('dashboardSection').style.display = 'none';
    }
    // Load data based on section
    if (section === 'rooms') loadRooms();
    if (section === 'gallery') loadGallery();
    if (section === 'booking') loadBookingForm();
}

function showDashboard() {
    if (!currentUser) {
        openLogin();
        return;
    }
    document.querySelectorAll('.section').forEach(el => {
        if (el.id === 'dashboardSection') {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    });
    document.getElementById('dashboardSection').style.display = 'block';
    loadDashboardData();
}

function hideDashboard() {
    document.getElementById('dashboardSection').style.display = 'none';
    showSection('home');
}

// =============================================
// LOAD PUBLIC DATA
// =============================================
async function loadRooms() {
    loadingOverlay.classList.add('active');
    const rooms = await apiRequest('public_rooms');
    if (!rooms.error) {
        let html = '';
        rooms.forEach(room => {
            const statusBadge = room.status === 'available' ? 'available' : 'occupied';
            html += `
                <div class="room-card">
                    <img src="${room.image_url || 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=400'}" alt="${room.room_type}" />
                    <div class="content">
                        <h3>Room ${room.room_number}</h3>
                        <div class="room-type">${room.room_type} · ${room.capacity} ${room.capacity > 1 ? 'beds' : 'bed'}</div>
                        <div class="price">$${parseFloat(room.price || 0).toFixed(2)} <span style="font-size:0.8rem; font-weight:400; color:#4c6f96;">/ night</span></div>
                        <div class="facilities">${room.facilities ? room.facilities.split(',').map(f => `<i class="fas fa-check-circle"></i> ${f.trim()}`).join(' ') : ''}</div>
                        <p style="font-size:0.85rem; color:#4c6f96; margin:0.5rem 0;">${room.description || ''}</p>
                        <span class="badge ${statusBadge}" style="display:inline-block; margin-bottom:0.5rem;">${room.status}</span>
                        <button class="btn btn-primary btn-sm" onclick="bookRoom(${room.id})"><i class="fas fa-calendar-plus"></i> Book Now</button>
                    </div>
                </div>
            `;
        });
        roomGrid.innerHTML = html;
    }
    loadingOverlay.classList.remove('active');
}

async function loadGallery() {
    loadingOverlay.classList.add('active');
    const images = await apiRequest(`public_images&category=${currentGalleryFilter === 'all' ? '' : currentGalleryFilter}`);
    if (!images.error) {
        let html = '';
        images.forEach(img => {
            html += `
                <div class="gallery-item">
                    <img src="${img.image_url}" alt="${img.title}" loading="lazy" />
                    <div class="overlay">
                        <h4>${img.title}</h4>
                        <p>${img.description || ''}</p>
                    </div>
                </div>
            `;
        });
        galleryGrid.innerHTML = html || '<p style="text-align:center; color:#7a8aa3; grid-column:1/-1;">No images in this category</p>';
    }
    loadingOverlay.classList.remove('active');
}

function filterGallery(category) {
    currentGalleryFilter = category;
    document.querySelectorAll('.section-tabs .tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.textContent.toLowerCase() === category || (category === 'all' && btn.textContent === 'All'));
    });
    loadGallery();
}

async function loadBookingForm() {
    const rooms = await apiRequest('public_rooms');
    if (!rooms.error) {
        const select = document.getElementById('bookRoom');
        select.innerHTML = '<option value="">Choose a room...</option>';
        rooms.forEach(room => {
            const opt = document.createElement('option');
            opt.value = room.id;
            opt.textContent = `Room ${room.room_number} - ${room.room_type} ($${room.price}/night)`;
            select.appendChild(opt);
        });
    }
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('bookCheckIn').value = today;
    const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    document.getElementById('bookCheckOut').value = tomorrow;
}

function bookRoom(roomId) {
    showSection('booking');
    document.getElementById('bookRoom').value = roomId;
}

// =============================================
// DASHBOARD DATA
// =============================================
async function loadDashboardData() {
    if (!currentUser || currentUser.role !== 'admin') return;
    loadingOverlay.classList.add('active');
    try {
        // Stats
        const stats = await apiRequest('stats');
        if (!stats.error) {
            totalRoomsEl.textContent = stats.totalRooms || 0;
            occupiedCountEl.textContent = stats.occupied || 0;
            availableCountEl.textContent = stats.available || 0;
            guestCountEl.textContent = stats.guests || 0;
            pendingBookingsEl.textContent = stats.pendingBookings || 0;
        }

        // Rooms
        const rooms = await apiRequest('rooms');
        if (!rooms.error) {
            let html = '';
            rooms.forEach(room => {
                const guestName = room.guest_name || '—';
                const statusClass = room.status === 'available' ? 'available' : room.status === 'occupied' ? 'occupied' : '';
                html += `
                    <tr>
                        <td><strong>${room.room_number}</strong></td>
                        <td>${room.room_type}</td>
                        <td>${room.capacity}</td>
                        <td>$${parseFloat(room.price || 0).toFixed(2)}</td>
                        <td><span class="badge ${statusClass}">${room.status}</span></td>
                        <td>${guestName}</td>
                        <td style="text-align:right;" class="action-icons">
                            <i class="fas fa-edit" data-action="editRoom" data-id="${room.id}"></i>
                            <i class="fas fa-trash" data-action="deleteRoom" data-id="${room.id}"></i>
                            ${room.status === 'available' ? `<i class="fas fa-user-check" data-action="checkin" data-id="${room.id}" style="color:#1e7b3a;"></i>` : ''}
                            ${room.status === 'occupied' ? `<i class="fas fa-user-minus" data-action="checkout" data-id="${room.id}" style="color:#b13e3e;"></i>` : ''}
                        </td>
                    </tr>
                `;
            });
            roomTableBody.innerHTML = html;
        }

        // Guests
        const guests = await apiRequest('guests');
        if (!guests.error) {
            let html = '';
            for (const g of guests) {
                const payments = await apiRequest(`payments&guest_id=${g.id}`);
                const paymentStatus = payments && payments.length > 0 ?
                    payments.map(p => `<span class="badge ${p.status}">$${parseFloat(p.amount).toFixed(2)} ${p.status}</span>`).join(' ') :
                    '<span style="color:#7a8aa3;">—</span>';
                html += `
                    <tr>
                        <td><i class="fas fa-user-circle" style="color:#1a5fb0; margin-right:8px;"></i>${g.name}</td>
                        <td>Room ${g.room_number}</td>
                        <td>${g.check_in}</td>
                        <td>${g.phone || '—'}</td>
                        <td>${paymentStatus}</td>
                        <td style="text-align:right;" class="action-icons">
                            <i class="fas fa-trash" data-action="deleteGuest" data-id="${g.id}"></i>
                        </td>
                    </tr>
                `;
            }
            guestTableBody.innerHTML = html;
        }

        // Bookings
        const bookings = await apiRequest('bookings');
        if (!bookings.error) {
            let html = '';
            bookings.forEach(b => {
                const statusClass = b.status === 'confirmed' ? 'confirmed' : b.status === 'cancelled' ? 'cancelled' : 'pending';
                html += `
                    <tr>
                        <td>${b.guest_name}</td>
                        <td>Room ${b.room_number}</td>
                        <td>${b.check_in}</td>
                        <td>${b.check_out}</td>
                        <td><span class="badge ${statusClass}">${b.status}</span></td>
                        <td style="text-align:right;" class="action-icons">
                            ${b.status === 'pending' ? `<i class="fas fa-check" data-action="confirmBooking" data-id="${b.id}" style="color:#1e7b3a;"></i>` : ''}
                            ${b.status !== 'cancelled' ? `<i class="fas fa-times" data-action="cancelBooking" data-id="${b.id}" style="color:#b13e3e;"></i>` : ''}
                        </td>
                    </tr>
                `;
            });
            bookingTableBody.innerHTML = html;
        }

        // Images
        const images = await apiRequest('images');
        if (!images.error) {
            let html = '';
            images.forEach(img => {
                html += `
                    <tr>
                        <td>${img.title}</td>
                        <td>${img.category}</td>
                        <td><img src="${img.image_url}" alt="${img.title}" style="width:60px; height:40px; object-fit:cover; border-radius:8px;" /></td>
                        <td style="text-align:right;" class="action-icons">
                            <i class="fas fa-trash" data-action="deleteImage" data-id="${img.id}"></i>
                        </td>
                    </tr>
                `;
            });
            imageTableBody.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
    loadingOverlay.classList.remove('active');
}

// =============================================
// ADMIN ACTIONS
// =============================================
async function addRoom(data) {
    const result = await apiRequest('room', 'POST', data);
    if (result.success) {
        loadDashboardData();
        return true;
    }
    alert(result.error || 'Failed to add room');
    return false;
}

async function updateRoom(id, data) {
    const result = await apiRequest(`room&id=${id}`, 'PUT', data);
    if (result.success) {
        loadDashboardData();
        return true;
    }
    alert(result.error || 'Failed to update room');
    return false;
}

async function deleteRoom(id) {
    if (!confirm('Delete this room?')) return;
    const result = await apiRequest(`room&id=${id}`, 'DELETE');
    if (result.success) loadDashboardData();
    else alert(result.error || 'Failed to delete room');
}

async function checkInGuest(roomId, data) {
    const result = await apiRequest('checkin', 'POST', { room_id: roomId, ...data });
    if (result.success) {
        loadDashboardData();
        return true;
    }
    alert(result.error || 'Check-in failed');
    return false;
}

async function checkoutGuest(roomId) {
    if (!confirm('Checkout guest?')) return;
    const result = await apiRequest(`checkout&room_id=${roomId}`, 'POST');
    if (result.success) loadDashboardData();
    else alert(result.error || 'Checkout failed');
}

async function deleteGuest(id) {
    if (!confirm('Remove guest?')) return;
    const result = await apiRequest(`guest&id=${id}`, 'DELETE');
    if (result.success) loadDashboardData();
    else alert(result.error || 'Failed to remove guest');
}

async function confirmBooking(id) {
    const result = await apiRequest(`booking&id=${id}`, 'PUT', { status: 'confirmed' });
    if (result.success) loadDashboardData();
    else alert(result.error || 'Failed to confirm booking');
}

async function cancelBooking(id) {
    if (!confirm('Cancel this booking?')) return;
    const result = await apiRequest(`booking&id=${id}`, 'PUT', { status: 'cancelled' });
    if (result.success) loadDashboardData();
    else alert(result.error || 'Failed to cancel booking');
}

async function addImage(data) {
    const result = await apiRequest('image', 'POST', data);
    if (result.success) {
        loadDashboardData();
        loadGallery();
        return true;
    }
    alert(result.error || 'Failed to add image');
    return false;
}

async function deleteImage(id) {
    if (!confirm('Delete this image?')) return;
    const result = await apiRequest(`image&id=${id}`, 'DELETE');
    if (result.success) {
        loadDashboardData();
        loadGallery();
    } else alert(result.error || 'Failed to delete image');
}

async function addPayment(data) {
    const result = await apiRequest('payment', 'POST', data);
    if (result.success) {
        loadDashboardData();
        return true;
    }
    alert(result.error || 'Failed to add payment');
    return false;
}

async function registerUser(data) {
    const result = await apiRequest('register', 'POST', data);
    if (result.success) {
        alert('User registered successfully!');
        registerModal.classList.remove('active');
        registerForm.reset();
        return true;
    }
    alert(result.error || 'Registration failed');
    return false;
}

// =============================================
// EVENT LISTENERS
// =============================================
// Login
loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const username = loginUsername.value.trim();
    const password = loginPassword.value.trim();
    login(username, password);
});

// Booking
bookingForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
        guest_name: document.getElementById('bookName').value.trim(),
        email: document.getElementById('bookEmail').value.trim(),
        phone: document.getElementById('bookPhone').value.trim(),
        room_id: parseInt(document.getElementById('bookRoom').value),
        check_in: document.getElementById('bookCheckIn').value,
        check_out: document.getElementById('bookCheckOut').value,
        message: document.getElementById('bookMessage').value.trim()
    };
    if (!data.guest_name || !data.email || !data.phone || !data.room_id) {
        alert('Please fill in all required fields.');
        return;
    }
    loadingOverlay.classList.add('active');
    const result = await apiRequest('public_booking', 'POST', data);
    loadingOverlay.classList.remove('active');
    if (result.success) {
        alert('Booking submitted successfully! We will contact you soon.');
        bookingForm.reset();
        loadBookingForm();
    } else {
        alert(result.error || 'Booking failed. Please try again.');
    }
});

// Room Modal
document.getElementById('addRoomBtn').addEventListener('click', function() {
    editingRoomId = null;
    document.getElementById('roomModalTitle').innerHTML = '<i class="fas fa-door-open" style="color:#1a5fb0;"></i> Add Room';
    roomForm.reset();
    roomModal.classList.add('active');
});

roomModalCancel.addEventListener('click', () => roomModal.classList.remove('active'));
roomModal.addEventListener('click', function(e) {
    if (e.target === roomModal) roomModal.classList.remove('active');
});

roomForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
        room_number: document.getElementById('roomNumber').value.trim(),
        room_type: document.getElementById('roomType').value,
        capacity: parseInt(document.getElementById('roomCapacity').value),
        price: parseFloat(document.getElementById('roomPrice').value) || 0,
        image_url: document.getElementById('roomImage').value.trim(),
        description: document.getElementById('roomDescription').value.trim(),
        facilities: document.getElementById('roomFacilities').value.trim(),
        status: document.getElementById('roomStatus').value
    };
    if (!data.room_number) { alert('Room number is required.'); return; }
    let success;
    if (editingRoomId) {
        success = await updateRoom(editingRoomId, data);
    } else {
        success = await addRoom(data);
    }
    if (success) {
        roomModal.classList.remove('active');
        roomForm.reset();
    }
});

// Image Modal
document.getElementById('addImageBtn').addEventListener('click', function() {
    imageForm.reset();
    imageModal.classList.add('active');
});

imageModalCancel.addEventListener('click', () => imageModal.classList.remove('active'));
imageModal.addEventListener('click', function(e) {
    if (e.target === imageModal) imageModal.classList.remove('active');
});

imageForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
        title: document.getElementById('imgTitle').value.trim(),
        image_url: document.getElementById('imgUrl').value.trim(),
        category: document.getElementById('imgCategory').value,
        description: document.getElementById('imgDescription').value.trim()
    };
    if (!data.title || !data.image_url) { alert('Title and image URL are required.'); return; }
    const success = await addImage(data);
    if (success) {
        imageModal.classList.remove('active');
        imageForm.reset();
    }
});

// Register User
document.getElementById('registerUserBtn').addEventListener('click', function() {
    registerForm.reset();
    registerModal.classList.add('active');
});
regCancelBtn.addEventListener('click', () => registerModal.classList.remove('active'));
registerModal.addEventListener('click', function(e) {
    if (e.target === registerModal) registerModal.classList.remove('active');
});

registerForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
        full_name: document.getElementById('regName').value.trim(),
        username: document.getElementById('regUsername').value.trim(),
        password: document.getElementById('regPassword').value.trim(),
        role: document.getElementById('regRole').value
    };
    if (!data.full_name || !data.username || !data.password) {
        alert('All fields are required.');
        return;
    }
    await registerUser(data);
});

// Payment Modal
document.getElementById('addPaymentBtn').addEventListener('click', async function() {
    const guests = await apiRequest('guests');
    if (guests.error) return;
    paymentGuest.innerHTML = '<option value="">Select guest</option>';
    guests.forEach(g => {
        const opt = document.createElement('option');
        opt.value = g.id;
        opt.textContent = `${g.name} (Room ${g.room_number})`;
        paymentGuest.appendChild(opt);
    });
    document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
    paymentForm.reset();
    paymentModal.classList.add('active');
});
paymentCancelBtn.addEventListener('click', () => paymentModal.classList.remove('active'));
paymentModal.addEventListener('click', function(e) {
    if (e.target === paymentModal) paymentModal.classList.remove('active');
});

paymentForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
        guest_id: parseInt(paymentGuest.value),
        amount: parseFloat(document.getElementById('paymentAmount').value),
        payment_date: document.getElementById('paymentDate').value,
        status: document.getElementById('paymentStatus').value,
        description: document.getElementById('paymentDescription').value.trim()
    };
    if (!data.guest_id || !data.amount || !data.payment_date) {
        alert('All fields required.');
        return;
    }
    const success = await addPayment(data);
    if (success) {
        paymentModal.classList.remove('active');
        paymentForm.reset();
    }
});

// Refresh
document.getElementById('refreshBtn').addEventListener('click', loadDashboardData);

// Check-in
document.getElementById('checkinBtn').addEventListener('click', async function() {
    const rooms = await apiRequest('rooms');
    if (rooms.error) return;
    const avail = rooms.filter(r => r.status === 'available');
    if (avail.length === 0) { alert('No available rooms.'); return; }
    const list = avail.map(r => `${r.id}:${r.room_number} ($${r.price})`).join(', ');
    const idInput = prompt(`Enter room ID (available: ${list})`);
    if (!idInput) return;
    const roomId = parseInt(idInput);
    const room = avail.find(r => r.id === roomId);
    if (!room) { alert('Invalid room.'); return; }
    const name = prompt('Guest name:');
    if (!name || !name.trim()) return;
    const phone = prompt('Phone number (optional):') || '';
    const email = prompt('Email (optional):') || '';
    await checkInGuest(roomId, { name: name.trim(), phone, email });
});

// =============================================
// EVENT DELEGATION FOR ACTION ICONS
// =============================================
document.addEventListener('click', function(e) {
    const target = e.target.closest('[data-action]');
    if (!target) return;
    const action = target.dataset.action;
    const id = parseInt(target.dataset.id);
    if (!currentUser || currentUser.role !== 'admin') return;

    if (action === 'editRoom') {
        // Open room modal with data
        const row = target.closest('tr');
        const cells = row.querySelectorAll('td');
        document.getElementById('roomModalTitle').innerHTML = '<i class="fas fa-edit" style="color:#1a5fb0;"></i> Edit Room';
        document.getElementById('roomNumber').value = cells[0].textContent.trim();
        document.getElementById('roomType').value = cells[1].textContent.trim();
        document.getElementById('roomCapacity').value = cells[2].textContent.trim();
        document.getElementById('roomPrice').value = parseFloat(cells[3].textContent.replace('$', ''));
        document.getElementById('roomStatus').value = cells[4].textContent.trim();
        editingRoomId = id;
        roomModal.classList.add('active');
    } else if (action === 'deleteRoom') {
        deleteRoom(id);
    } else if (action === 'checkin') {
        const name = prompt('Guest name:');
        if (name && name.trim()) {
            const phone = prompt('Phone (optional):') || '';
            const email = prompt('Email (optional):') || '';
            checkInGuest(id, { name: name.trim(), phone, email });
        }
    } else if (action === 'checkout') {
        checkoutGuest(id);
    } else if (action === 'deleteGuest') {
        deleteGuest(id);
    } else if (action === 'deleteImage') {
        deleteImage(id);
    } else if (action === 'confirmBooking') {
        confirmBooking(id);
    } else if (action === 'cancelBooking') {
        cancelBooking(id);
    }
});

// =============================================
// INITIALIZATION
// =============================================
// Show home section by default
showSection('home');
// Load rooms
loadRooms();
// Load gallery
loadGallery();
// Load booking form
loadBookingForm();

console.log('HostelPro ready! Login with admin/admin123');
</script>

</body>
</html>