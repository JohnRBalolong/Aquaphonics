<?php
// Get the JSON input
$data = json_decode(file_get_contents("php://input"), true);
$folderName = $data['ownerName']; // The sanitized folder name

// Define the directory path (make sure this path is writable)
$baseDir = 'user_directories/'; // Ensure this directory exists and is writable
$fullPath = $baseDir . $folderName;

// Create the directory if it doesn't exist
if (!file_exists($fullPath)) {
    if (mkdir($fullPath, 0777, true)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create directory.']);
    }
} else {
    echo json_encode(['success' => true]); // Directory already exists
}
?>