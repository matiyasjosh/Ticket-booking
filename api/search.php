<?php
require 'db.php'; // Adjust the path to your database connection file

if (isset($_GET['query'])) {
    $search = trim($_GET['query']);
    
    if (!empty($search)) {
        $stmt = $conn->prepare("SELECT id, title, image FROM movies WHERE title LIKE ? LIMIT 5");
        $searchTerm = "%$search%";
        $stmt->bind_param("s", $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $movies = [];
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        
        echo json_encode($movies);
    }
}
?>