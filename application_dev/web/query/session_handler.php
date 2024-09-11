<?php
session_start();

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Log the received data for debugging
file_put_contents('php://stderr', print_r($data, TRUE)); // This logs to the error log

if (!empty($data)) {
    if (isset($data['isAdmin']) && $data['isAdmin'] === true) {
        // If the user is an admin
        $_SESSION['admin'] = [
            'uid' => $data['uid'],
            'email' => $data['email']
        ];
    } else {
        // If the user is not an admin, store as a regular user
        $_SESSION['user'] = [
            'uid' => $data['uid'],
            'email' => $data['email'],
            'owner' => $data['owner'],
            'folderName' => $data['folderName'],
            'phone' => $data['phone'],
            'doin' => $data['doin']
        ];
    }

    // Send success response
    echo json_encode(['success' => true]);
} else {
    // Send error response if no data was received
    echo json_encode(['success' => false, 'message' => 'No data received']);
}
?>
