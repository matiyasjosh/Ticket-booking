<?php
// After successful booking, return JSON response
$ticketNumber = 'TIX' . date('Ymd') . rand(1000, 9999);

// Process booking and save to database here...

// Return ticket data as JSON
$ticketData = [
    'success' => true,
    'ticketNumber' => $ticketNumber,
    'movieTitle' => 'Dune: Part Two', // Get from database
    'date' => date('Y-m-d'),
    'showtime' => '6:00 PM', // Get from form/database
    'seat' => 'A12', // Get from form/database
];

header('Content-Type: application/json');
echo json_encode($ticketData);
exit;
?>