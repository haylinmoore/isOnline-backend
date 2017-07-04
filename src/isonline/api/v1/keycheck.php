<?php
require_once '../../../global.php';

// Internal API output
$registered = false;
$bot = false;
$valid = false;
$error = true;

// Assume inputs $user and $key

// Set strict error reporting (to disable PHP warnings for bad logins)
mysqli_report(MYSQLI_REPORT_STRICT);

$mysqli = false;
try {
    $mysqli = new mysqli(mysqli_host, mysqli_user, mysqli_pass, mysqli_database);
} catch (Exception $ex) {
    // Couldn't connect to database
    $error = true;
}

if ($mysqli) {
    // Connected successfully to database
    
    if (($stmt_check = $mysqli->prepare("SELECT keycode,log,bot FROM isonline WHERE user=? LIMIT 1"))) {
        
        $stmt_check->bind_param('s', $user);
        
        $stmt_check->execute();
        
        $result = $stmt_check->get_result();
        
        $error = false;
        
        if (($row = $result->fetch_object())) {
            // User registered
            
            // Check user wasn't previously detected as a bot
            if ($row->bot === 0) {
                // User not previously detected as bot
                
                if (hash('sha256', $key) === $row->keycode) {
                    // Correct key

                    $valid = true;
                    $registered = true;
                    $bot = false;

                    // Bot detection:
                    // In order to be marked as a bot, a user must send more than
                    // 20 requests inside a 10 second time period
                    // (So an average of more than 2 request per second)

                    // Get current log
                    $data = [];

                    if ($row->log !== null) {
                        // Populate existing data
                        $data = json_decode($row->log);
                    }

                    // Add current timestamp to data
                    $data[] = time();

                    if (count($data) <= 19) {
                        // Array not full, so don't remove any items

                        // Do no validation - not enough data
                    } else {
                        // Array full, so remove first item
                        $removed = array_shift($data);

                        // Number of seconds between last 20 requests
                        $rate = $data[count($data) - 1] - $removed;

                        if ($rate < 10) {
                            // Average of more than 2 request per second (over last 20 requests)

                            // Mark user as bot
                            $bot = true;
                        }
                    }

                    // Update table data
                    if (($stmt_update = $mysqli->prepare("UPDATE isonline SET log=?,bot=? WHERE user=? LIMIT 1"))) {
                        // Get JSON data
                        $json_data = json_encode($data);

                        $stmt_update->bind_param("sis", $json_data, $bot, $user);

                        $stmt_update->execute();

                        if ($stmt_update->affected_rows === 1) {
                            // Updated successfully
                        } else {
                            // Shouldn't happen, as we already validated the user exists
                            $error = true;
                        }
                    }
                } else {
                    // Incorrect key

                    $valid = false;
                    $registered = true;
                    $bot = false;
                }
            } else {
                // User previously detected as bot
                
                $valid = false;
                $registered = true;
                $bot = true;
            }
        } else {
            // User not registered
            
            $valid = false;
            $registered = false;
            $bot = false;
        }
    }
}