<?php
session_start(); // Add this line
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'signup') {
        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Check if email exists
        $check = mysqli_prepare($conn, "SELECT email FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        
        if (mysqli_stmt_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists!']);
            exit;
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Registration successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed!']);
        }
    }

    if ($data['action'] === 'login') {
        $email = trim($data['email']);
        $password = $data['password'];
    
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
    
        if ($user && password_verify($password, $user['password'])) {
            // Start session and set session variables
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful!',
                'role' => $user['role']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials!']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get_movies') {
        $result = mysqli_query($conn, "SELECT * FROM movies");
        $movies = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($movies);
    }
    
    if ($action === 'get_users') {
        $result = mysqli_query($conn, "SELECT id, username, email, role FROM users");
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($users);
    }
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
// Movies API
if (isset($_POST['action']) && $_POST['action'] === 'add_movie' && isAdmin()) {
    $uploadDir = '../uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    try {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $duration = (int)$_POST['duration'];
        $price = (float)$_POST['price'];

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        if (!isset($_FILES['image'])) {
            throw new Exception('No image file uploaded');
        }

        $file = $_FILES['image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload error: ' . $file['error']);
        }

        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('File too large');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('movie_') . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file');
        }

        $stmt = mysqli_prepare($conn, 
            "INSERT INTO movies (title, description, duration, price, image) 
            VALUES (?, ?, ?, ?, ?)");
            
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssids", 
            $title, $description, $duration, $price, $fileName);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to execute statement: ' . mysqli_stmt_error($stmt));
        }

        echo json_encode(['success' => true, 'message' => 'Movie added successfully']);

    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error adding movie: ' . $e->getMessage()
        ]);
    }
    exit;
}
// User Management
if ($_GET['action'] === 'get_users' && isAdmin()) {
    try {
        $stmt = mysqli_prepare($conn, "SELECT id, username, email, role FROM users");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($users);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
// Booking Management
if ($_POST['action'] === 'update_booking' && isAdmin()) {
    $bookingId = (int)$_POST['id'];
    $newStatus = mysqli_real_escape_string($conn, $_POST['status']);
    
    $stmt = mysqli_prepare($conn, "UPDATE bookings SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $newStatus, $bookingId);
    mysqli_stmt_execute($stmt);
    
    echo json_encode(['success' => true]);
    exit;
}

// GET Endpoints
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Movie Analytics
    if ($_GET['action'] === 'movie_analytics' && isAdmin()) {
        $analytics = [];
        
        // Total Movies
        $result = mysqli_query($conn, "SELECT COUNT(*) AS total_movies FROM movies");
        $analytics['total_movies'] = mysqli_fetch_assoc($result)['total_movies'];
        
        // Revenue by Movie
        $result = mysqli_query($conn,
            "SELECT m.title, SUM(b.total) AS revenue 
            FROM bookings b
            JOIN movies m ON b.movie_id = m.id
            GROUP BY m.id");
        $analytics['revenue_by_movie'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Bookings per Movie
        $result = mysqli_query($conn,
            "SELECT m.title, COUNT(b.id) AS bookings 
            FROM movies m
            LEFT JOIN bookings b ON m.id = b.movie_id
            GROUP BY m.id");
        $analytics['bookings_per_movie'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        echo json_encode($analytics);
        exit;
    }

    // User Management
    if ($_GET['action'] === 'get_users' && isAdmin()) {
        $result = mysqli_query($conn, "SELECT id, username, email, role FROM users");
        echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
        exit;
    }

    // Booking Management
    if ($_GET['action'] === 'get_bookings' && isAdmin()) {
        $result = mysqli_query($conn,
            "SELECT b.id, u.username, m.title, b.seats, b.total, b.status, b.created_at 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN movies m ON b.movie_id = m.id");
        echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
        exit;
    }
    // Update Movie
    if (isset($_POST['action']) && $_POST['action'] === 'update_movie' && isAdmin()) {
        try {
            $movieId = (int)$_POST['movie_id'];
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $duration = (int)$_POST['duration'];
            $price = (float)$_POST['price'];

            $updateFields = [];
            $params = [];
            $types = '';

            // Build dynamic update query
            $updateFields[] = "title = ?";
            $params[] = $title;
            $types .= 's';
            
            $updateFields[] = "description = ?";
            $params[] = $description;
            $types .= 's';
            
            $updateFields[] = "duration = ?";
            $params[] = $duration;
            $types .= 'i';
            
            $updateFields[] = "price = ?";
            $params[] = $price;
            $types .= 'd';

            // Handle image update
            if (!empty($_FILES['image']['name'])) {
                // Existing image upload code...
                $updateFields[] = "image = ?";
                $params[] = $fileName;
                $types .= 's';
            }

            $params[] = $movieId;
            $types .= 'i';

            $query = "UPDATE movies SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Movie updated successfully']);
            } else {
                throw new Exception('Update failed: ' . mysqli_stmt_error($stmt));
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // Delete Movie
    if (isset($_POST['action']) && $_POST['action'] === 'delete_movie' && isAdmin()) {
        try {
            $movieId = (int)$_POST['id'];
            
            // First get image path to delete file
            $result = mysqli_query($conn, "SELECT image FROM movies WHERE id = $movieId");
            $movie = mysqli_fetch_assoc($result);
            if ($movie) {
                $imagePath = '../uploads/' . $movie['image'];
                if (file_exists($imagePath)) unlink($imagePath);
            }

            $stmt = mysqli_prepare($conn, "DELETE FROM movies WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $movieId);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Movie deleted successfully']);
            } else {
                throw new Exception('Delete failed: ' . mysqli_stmt_error($stmt));
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}


mysqli_close($conn);
?>