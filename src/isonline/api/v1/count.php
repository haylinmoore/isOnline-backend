<?php
require_once '../../../global.php';

// Create output variables
$count = null;

$output = [
    'count' => &$count
];

// Default to failed response
http_response_code(500);

$mysqli = false;
try {
    $mysqli = new mysqli(mysqli_host, mysqli_user, mysqli_pass, mysqli_database);
} catch (Exception $ex) {
    // Couldn't connect to database
    $error = true;
}

if ($mysqli) {
    // Connected successfully to database

    if (($result = $mysqli->query("SELECT COUNT(*) FROM isonline"))) { 
        $count = $result->fetch_array()['COUNT(*)'];
        http_response_code(200);
    }
    
    // Close DB connection
    $mysqli->close();
}

// Set headers
header('Access-Control-Allow-Origin: https://scratch.mit.edu');
header('Content-Type: application/json');

// Output data
echo json_encode($output);