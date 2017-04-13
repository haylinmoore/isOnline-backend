<?php
require_once '../../../global.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://scratch.mit.edu');

// Get inputs
$user = filter_input(INPUT_GET, 'user');
$key = filter_input(INPUT_GET, 'key');
$status = filter_input(INPUT_GET, 'status');

// Create output
$result = 'error';
$output = [
    'result' => &$result
];

// Set allowed status values
$whitelist = [
    'online',
    'absent',
    'dnd'
];

// Check proposed status is valid
if (in_array($status, $whitelist, true)) {
    // Status is whitelisted, accept
    
    // Test key is valid
    require_once 'keycheck.php';

    if (!$error && $mysqli) {
        // Connected successfully to database

        if ($registered) {
            // User exists

            if (!$bot) {
                // Not a bot

                if ($valid) {
                    // Correct key

                    // Update record
                    if (($stmt_update = $mysqli->prepare("UPDATE isonline SET timestamp=UNIX_TIMESTAMP(), status=? WHERE user=? LIMIT 1"))) {

                        // Bind update parameters
                        $stmt_update->bind_param(
                                "ss",
                                $status,
                                $user
                            );

                        $stmt_update->execute();

                        if ($stmt_update->affected_rows === 1) {
                            $result = 'success';
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
                    $result = 'incorrect key';
                    http_response_code(403);
                }
            } else {
                // Bot
                $result = 'bot';
                http_response_code(403);
            }
        } else {
            // User doesn't exist
            $result = 'not registered';
            http_response_code(404);
        }

        // Close DB connection
        $mysqli->close();
    } else {
        http_response_code(500);
    }
} else {
    // Status not in whitelist, reject
    
    http_response_code(403);
    $result = 'invalid status';
}

echo json_encode($output);