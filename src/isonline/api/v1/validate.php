<?php
require_once '../../../global.php';

// Headers
header('Access-Control-Allow-Origin: https://' . site_host . '/');
header('Content-Type: application/json');

// Get inputs
$user = strtolower(filter_input(INPUT_POST, 'user')); // The user to verify
$code = strtolower(filter_input(INPUT_POST, 'code')); // The verification code
$regenerate = filter_input(INPUT_POST, 'regenerate', FILTER_VALIDATE_BOOLEAN); // True to regenerate key on existing account

// Load comments on verification project
if (($html = @file_get_contents('https://scratch.mit.edu/site-api/comments/gallery/4100062/?page=1'))) {
    // HTML downloaded successfully
    
    $DOM = new DOMDocument;
    @$DOM->loadHTML($html);

    // Get all [loaded] comments (also includes replies)
    $comments = $DOM->getElementsByTagName('li');
    $comments_length = $comments->length;

    // Get current datetime
    $now = new DateTime();

    for ($i = 0; $i < $comments_length; $i++) {
        // Get div containing comment data
        $comment_data = $comments->item($i)->childNodes->item(1)->childNodes->item(5);

        // Get username of comment
        $comment_user = strtolower(trim($comment_data->childNodes->item(1)->childNodes->item(1)->nodeValue));

        // Get comment text
        $comment_content = strtolower(trim($comment_data->childNodes->item(3)->nodeValue));

        // Get comment time
        $comment_time = new DateTime($comment_data->childNodes->item(5)->childNodes->item(1)->getAttribute('title'));

        // Get difference in time to now
        $difference = $comment_time->diff($now);

        if($difference->days === 0 && $difference->h === 0 && $difference->i < 2) {
            if ($comment_user === $user && strpos($comment_content, $code) !== false) {
                // Match found - validation passed
                // Now add record

                // Set strict error reporting (to disable PHP warnings for bad logins)
                mysqli_report(MYSQLI_REPORT_STRICT);

                $mysqli = false;
                try {
                    $mysqli = new mysqli(mysqli_host, mysqli_user, mysqli_pass, mysqli_database);
                } catch (Exception $ex) {
                    // Couldn't connect to database
                    http_response_code(500);
                    echo json_encode([
                        'result' => 'error',
                        'status' => 3,
                        'key' => null
                    ]);
                }

                if ($mysqli) {
                    // Connected successfully to database

                    // Create key
                    require 'keygen.php';

                    if (!$regenerate) {
                        // Create new account

                        $stmt = $mysqli->prepare("INSERT IGNORE INTO isonline (user, keycode, timestamp, status) VALUES (?, ?, 0, 'online')");

                        if ($stmt) {
                            // Bind insert parameters
                            $stmt->bind_param(
                                    "ss",
                                    $user,
                                    $keyhash
                                );
                        }
                    } else {
                        // Update key on existing account

                        $stmt = $mysqli->prepare("UPDATE isonline SET keycode=? WHERE user=? LIMIT 1");

                        if ($stmt) {
                            // Bind update parameters
                            $stmt->bind_param(
                                    "ss",
                                    $keyhash,
                                    $user
                                );
                        }
                    }

                    if ($stmt) {

                        $stmt->execute();

                        if ($stmt->affected_rows === 1) {
                            echo json_encode([
                                'result' => 'success',
                                'status' => 0,
                                'key' => $key
                            ]);
                        } else {
                            // Row already exists

                            $result = 'user already exists';
                            if ($regenerate) {
                                $result = 'user not registered';
                            }

                            echo json_encode([
                                'result' => $result,
                                'status' => 2,
                                'key' => null
                            ]);
                        }
                    } else {
                        // Statement isn't valid
                        http_response_code(500);
                        echo json_encode([
                            'result' => 'error',
                            'status' => 3,
                            'key' => null
                        ]);
                    }

                    // Close DB connection
                    $mysqli->close();
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'result' => 'error',
                        'status' => 3,
                        'key' => null
                    ]);
                }

                // Matching comment already found, don't bother checking others
                die('');
            }
        } else {
            // Comments are sorted chronologically, so any comments after this
            // will also be outside the allowed range

            echo json_encode([
                'result' => 'fail',
                'status' => 1,
                'key' => null
            ]);

            die('');
        }
    }

    echo json_encode([
        'result' => 'fail',
        'status' => 1,
        'key' => null
    ]);
} else {
    // Timeout / connection error
    
    echo json_encode([
        'result' => 'error',
        'status' => 4,
        'key' => null
    ]);
}