<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Prevent any output before intended JSON response
ob_start();

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function sendJsonResponse($data) {
    ob_clean(); // Clear any previous output
    echo json_encode($data);
    exit;
}

// ------------------ JSON-Based POST Requests ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data && isset($data['action'])) {
        switch ($data['action']) {
            case 'signup':
                try {
                    $username = trim($data['username']);
                    $email = trim($data['email']);
                    $password = password_hash($data['password'], PASSWORD_DEFAULT);

                    $check = mysqli_prepare($conn, "SELECT email FROM users WHERE email = ?");
                    mysqli_stmt_bind_param($check, "s", $email);
                    mysqli_stmt_execute($check);
                    mysqli_stmt_store_result($check);
                    
                    if (mysqli_stmt_num_rows($check) > 0) {
                        sendJsonResponse(['success' => false, 'message' => 'Email already exists!']);
                    }

                    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'Registration successful!']);
                    } else {
                        throw new Exception('Registration failed!');
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'login':
                try {
                    $email = trim($data['email']);
                    $password = $data['password'];

                    $stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE email = ?");
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $user = mysqli_fetch_assoc($result);

                    if ($user && password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_role'] = $user['role'];
                        
                        sendJsonResponse([
                            'success' => true,
                            'message' => 'Login successful!',
                            'role' => $user['role']
                        ]);
                    } else {
                        sendJsonResponse(['success' => false, 'message' => 'Invalid credentials!']);
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'update_user':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $userId = (int)$data['id'];
                    $role = mysqli_real_escape_string($conn, $data['role']);
                    
                    $stmt = mysqli_prepare($conn, "UPDATE users SET role = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "si", $role, $userId);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'User role updated successfully']);
                    } else {
                        throw new Exception('Failed to update user role');
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'delete_user':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $userId = (int)$data['id'];
                    
                    // Start a transaction
                    mysqli_begin_transaction($conn);
                    
                    // Disable foreign key checks
                    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
                    
                    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $userId);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        // Re-enable foreign key checks and commit the transaction
                        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
                        mysqli_commit($conn);
                        sendJsonResponse(['success' => true, 'message' => 'User deleted successfully']);
                    } else {
                        throw new Exception('Failed to delete user');
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'ban_user':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $userId = (int)$data['id'];
                    $stmt = mysqli_prepare($conn, "UPDATE users SET role = 'banned' WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $userId);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'User banned successfully']);
                    } else {
                        throw new Exception('Failed to ban user');
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'update_booking':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $bookingId = (int)$data['id'];
                    $newStatus = mysqli_real_escape_string($conn, $data['status']);
                    
                    $stmt = mysqli_prepare($conn, "UPDATE bookings SET status = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "si", $newStatus, $bookingId);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'Booking updated successfully']);
                    } else {
                        throw new Exception('Failed to update booking');
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            // New JSON case for delete_movie
            case 'delete_movie':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $movieId = (int)$data['id'];

                    // Fetch the movie's image path
                    $stmt = mysqli_prepare($conn, "SELECT image FROM movies WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $movieId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $movie = mysqli_fetch_assoc($result);
                    
                    if ($movie) {
                        $imagePath = '../uploads/' . $movie['image'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }

                    // Delete the movie from database
                    $stmt = mysqli_prepare($conn, "DELETE FROM movies WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $movieId);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'Movie deleted successfully']);
                    } else {
                        throw new Exception('Failed to delete movie');
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
        }
    }
}

// ------------------ Form POST Requests ------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_SERVER['CONTENT_TYPE']) || strpos($_SERVER['CONTENT_TYPE'], 'application/json') === false)) {
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_movie':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $uploadDir = '../uploads/';
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $maxSize = 5 * 1024 * 1024;

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

                    $title = mysqli_real_escape_string($conn, $_POST['title']);
                    $description = mysqli_real_escape_string($conn, $_POST['description']);
                    $duration = (int)$_POST['duration'];
                    $price = (float)$_POST['price'];
                    $show_time = date('Y-m-d H:i:s', strtotime($_POST['show_time']));

                    $stmt = mysqli_prepare($conn, 
                        "INSERT INTO movies (title, description, duration, price, image, show_time) 
                         VALUES (?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "ssidss", $title, $description, $duration, $price, $targetPath, $show_time);

                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'Movie added successfully']);
                    } else {
                        throw new Exception('Failed to add movie: ' . mysqli_stmt_error($stmt));
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'update_movie':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $movieId = (int)$_POST['movie_id'];
                    $title = mysqli_real_escape_string($conn, $_POST['title']);
                    $description = mysqli_real_escape_string($conn, $_POST['description']);
                    $duration = (int)$_POST['duration'];
                    $price = (float)$_POST['price'];
                    $show_time = mysqli_real_escape_string($conn, $_POST['show_time']);

                    $updateFields = [];
                    $params = [];
                    $types = '';

                    // Basic fields
                    $updateFields[] = "title = ?";
                    $updateFields[] = "description = ?";
                    $updateFields[] = "duration = ?";
                    $updateFields[] = "price = ?";
                    $updateFields[] = "show_time = ?";
                    
                    $params[] = $title;
                    $params[] = $description;
                    $params[] = $duration;
                    $params[] = $price;
                    $params[] = $show_time;
                    $types .= "ssids";

                    // Handle image if provided
                    if (!empty($_FILES['image']['name'])) {
                        $uploadDir = '../uploads/';
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        $maxSize = 5 * 1024 * 1024;
                        
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

                        $updateFields[] = "image = ?";
                        $params[] = $fileName;
                        $types .= "s";
                    }

                    // Add movieId to parameters
                    $params[] = $movieId;
                    $types .= "i";

                    $query = "UPDATE movies SET " . implode(', ', $updateFields) . " WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, $types, ...$params);

                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'Movie updated successfully']);
                    } else {
                        throw new Exception('Update failed: ' . mysqli_stmt_error($stmt));
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;

            case 'delete_movie':
                if (!isAdmin()) {
                    sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
                }
                
                try {
                    $movieId = (int)$_POST['id'];

                    // Fetch the movie's image path
                    $stmt = mysqli_prepare($conn, "SELECT image FROM movies WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $movieId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $movie = mysqli_fetch_assoc($result);
                    
                    if ($movie) {
                        $imagePath = '../uploads/' . $movie['image'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }

                    // Delete the movie from database
                    $stmt = mysqli_prepare($conn, "DELETE FROM movies WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "i", $movieId);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        sendJsonResponse(['success' => true, 'message' => 'Movie deleted successfully']);
                    } else {
                        throw new Exception('Failed to delete movie');
                    }
                } catch (Exception $e) {
                    sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
                break;
        }
    }
}

// ------------------ GET Requests ------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_movies':
            try {
                $stmt = mysqli_prepare($conn, "SELECT * FROM movies");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $movies = mysqli_fetch_all($result, MYSQLI_ASSOC);
                sendJsonResponse($movies);
            } catch (Exception $e) {
                sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'get_users':
            if (!isAdmin()) {
                sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
            }
            
            try {
                $stmt = mysqli_prepare($conn, "SELECT id, username, email, role FROM users");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
                sendJsonResponse($users);
            } catch (Exception $e) {
                sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'get_bookings':
            if (!isAdmin()) {
                sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
            }
            
            try {
                $stmt = mysqli_prepare($conn,
                    "SELECT b.id, u.username, m.title, b.seats, b.total, b.status, b.created_at 
                     FROM bookings b
                     JOIN users u ON b.user_id = u.id
                     JOIN movies m ON b.movie_id = m.id");
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
                sendJsonResponse($bookings);
            } catch (Exception $e) {
                sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'movie_analytics':
            if (!isAdmin()) {
                sendJsonResponse(['success' => false, 'message' => 'Unauthorized access']);
            }
            
            try {
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
                
                sendJsonResponse($analytics);
            } catch (Exception $e) {
                sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
    }
}

// Check if the required parameters are provided
if (isset($_GET['action']) || $_GET['action'] == 'get_movie' || isset($_GET['id'])) {
    $movieId = intval($_GET['id']); // Sanitize input
    // Validate the movie ID
    if ($movieId <= 0) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid movie ID']);
        exit();
    }

    // Fetch the movie by ID
    $query = "SELECT * FROM movies WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Database error']);
        exit();
    }

    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Movie not found']);
        exit();
    }

    $movie = $result->fetch_assoc();

    // Convert show_date and show_time to appropriate formats if needed
    if ($movie['show_date'] === 'now_showing') {
        $movie['show_date'] = 'Now Showing';
    } else {
        $movie['show_date'] = date('Y-m-d', strtotime($movie['show_date']));
    }

    if ($movie['show_time']) {
        $movie['show_time'] = date('Y-m-d H:i:s', strtotime($movie['show_time']));
    }

    // Return the movie data as JSON
    http_response_code(200); // OK
    echo json_encode($movie);

    // Close the statement and connection
    $stmt->close();
}

mysqli_close($conn);
?>
