<?php
require_once '../../../global.php';

// Create output variables
$timestamp = null;
$status = 'error';

$output = [
    'timestamp' => &$timestamp,
    'status'    => &$status
];

// Default to failed response
http_response_code(500);

// Get inputs
$user = filter_input(INPUT_GET, 'user');
$key = filter_input(INPUT_GET, 'key');
$other = filter_input(INPUT_GET, 'other');

// Test key is valid
require_once 'keycheck.php';

if (!$error && $mysqli) {
    // Connected successfully to database

    if ($registered) {
        // User is registered
        
        if (!$bot) {
            // Not a bot
            
            if ($valid) {
                // Key is correct

                if (($stmt = $mysqli->prepare("SELECT timestamp,status FROM isonline WHERE user=? LIMIT 1"))) {

                    $stmt->bind_param('s', $other);

                    $stmt->execute();

                    $result = $stmt->get_result();

                    if (($row = $result->fetch_object())) {
                        // User found, get values

                        $timestamp = $row->timestamp;
                        $status = $row->status;

                        http_response_code(200);
                    } else {
                        // Other user not registered

                        $status = 'not registered';
                        http_response_code(404);
                    }
                }
            } else {
                // Incorrect key
                $status = 'incorrect key';
                http_response_code(403);
            }
        } else {
            // Bot
            $status = 'bot';
            http_response_code(403);
        }
    } else {
        // User not registered
        $status = 'incorrect key';
        http_response_code(403);
    }
    
    // Close DB connection
    $mysqli->close();
}

// Set headers
header('Access-Control-Allow-Origin: https://scratch.mit.edu');
header('Content-Type: application/json');

// Output data
echo json_encode($output);