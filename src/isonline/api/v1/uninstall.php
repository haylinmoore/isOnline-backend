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
    'result' => &$status
];

// Test key is valid
require_once 'keycheck.php';

if (!$error && $mysqli) {
    // Connected successfully to database

    if ($registered) {
        // User exists

        // Don't check if bot for uninstall
        
        if ($valid) {
            // Correct key

            // Delete record
            if (($stmt_delete = $mysqli->prepare("DELETE FROM isonline WHERE user=? LIMIT 1"))) {

                // Bind update parameters
                $stmt_delete->bind_param('s', $user);

                $stmt_delete->execute();

                if ($stmt_delete->affected_rows === 1) {
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