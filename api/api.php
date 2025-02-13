<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add movie handling
    if ($data['action'] === 'save_movie') {
        $id = $data['id'] ?? null;
        $title = mysqli_real_escape_string($conn, $data['title']);
        $description = mysqli_real_escape_string($conn, $data['description']);
        $duration = (int)$data['duration'];

        if ($id) {
            $stmt = mysqli_prepare($conn, "UPDATE movies SET title=?, description=?, duration=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssii", $title, $description, $duration, $id);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO movies (title, description, duration) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $duration);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    
    // Add user management functions
}

mysqli_close($conn);
?>