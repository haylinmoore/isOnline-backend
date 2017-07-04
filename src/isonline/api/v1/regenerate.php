<?php
require_once '../../../global.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://scratch.mit.edu');

// Get inputs
$user = filter_input(INPUT_GET, 'user');
$key = filter_input(INPUT_GET, 'key');

// Create output
$status = 'error';
$output = [
    'status' => &$status,
    'key' => &$key
];

// Test key is valid
require_once 'keycheck.php';

if (!$error && $mysqli) {
    // Connected successfully to database

    if ($registered) {
        // User exists

        // Don't check if bot
        
        if ($valid) {
            // Correct key

            // Generate new key
            require 'keygen.php';

            // Update record
            if (($stmt_update = $mysqli->prepare("UPDATE isonline SET keycode=? WHERE user=? LIMIT 1"))) {

                // Bind update parameters
                $stmt_update->bind_param(
                        "ss",
                        $keyhash,
                        $user
                    );

                $stmt_update->execute();

                if ($stmt_update->affected_rows === 1) {
                    $status = 'success';
                    http_response_code(200);
                } else {
                    // Shouldn't happen, as we already validated the user exists
                    http_response_code(500);
                }
            } else {
                http_response_code(500);
            }
        } else {
            // Incorrect key
            $status = 'incorrect key';
            http_response_code(403);
        }
    } else {
        // User doesn't exist
        $status = 'not registered';
        http_response_code(404);
    }

    // Close DB connection
    $mysqli->close();
} else {
    http_response_code(500);
}

echo json_encode($output);