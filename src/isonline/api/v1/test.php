<?php
require_once '../../../global.php';

// Get inputs
$user = filter_input(INPUT_GET, 'user');
$key = filter_input(INPUT_GET, 'key');

// Test key is valid
require_once 'keycheck.php';

// If necessary, close mysql connection
if ($mysqli) {
    $mysqli->close();
}

// Create output variables
$output = [
    'valid' => $valid
];

// Set headers
header('Access-Control-Allow-Origin: https://scratch.mit.edu');
header('Content-Type: application/json');

// Output data
echo json_encode($output);