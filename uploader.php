<?php
// Load configuration
$config = require_once 'config.php';

// Apply CORS headers if enabled
if ($config['enable_cors']) {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    if (in_array('*', $config['cors_origins']) || in_array($origin, $config['cors_origins'])) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Configuration
$target_dir = $config['upload_directory'];
$max_file_size = $config['max_file_size'];
$allowed_types = $config['allowed_mime_types'];
$allowed_extensions = $config['allowed_extensions'];
$directory_permissions = $config['directory_permissions'];
$max_filename_length = $config['max_filename_length'];
$add_timestamp = $config['add_timestamp'];
$response_format = $config['response_format'];

// Create uploads directory if it doesn't exist
if (!file_exists($target_dir)) {
    mkdir($target_dir, $directory_permissions, true);
}

// Check if file was uploaded
if (!isset($_FILES["image"]) || $_FILES["image"]["error"] != UPLOAD_ERR_OK) {
    $error_message = "Error: No file uploaded or upload error occurred.";
    sendResponse(400, $error_message, $response_format);
    exit;
}

$file = $_FILES["image"];
$file_name = $file["name"];
$file_tmp = $file["tmp_name"];
$file_size = $file["size"];
$file_type = $file["type"];

// Validate file size
if ($file_size > $max_file_size) {
    $max_size_mb = round($max_file_size / (1024 * 1024), 2);
    $error_message = "Error: File size exceeds the maximum limit of {$max_size_mb}MB.";
    sendResponse(400, $error_message, $response_format);
    exit;
}

// Validate file type by MIME type
if (!in_array(strtolower($file_type), $allowed_types)) {
    $error_message = "Error: Invalid file type. Only " . implode(', ', $allowed_extensions) . " images are allowed.";
    sendResponse(400, $error_message, $response_format);
    exit;
}

// Get file extension
$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Validate file extension
if (!in_array($file_extension, $allowed_extensions)) {
    $error_message = "Error: Invalid file extension. Only " . implode(', ', $allowed_extensions) . " images are allowed.";
    sendResponse(400, $error_message, $response_format);
    exit;
}

// Additional security: Verify it's actually an image using getimagesize
$image_info = @getimagesize($file_tmp);
if ($image_info === false) {
    $error_message = "Error: File is not a valid image.";
    sendResponse(400, $error_message, $response_format);
    exit;
}

// Sanitize filename
$sanitized_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", pathinfo($file_name, PATHINFO_FILENAME));
$sanitized_name = substr($sanitized_name, 0, $max_filename_length); // Limit filename length

// Build filename
if ($add_timestamp) {
    $new_file_name = $sanitized_name . '_' . time() . '.' . $file_extension;
} else {
    $new_file_name = $sanitized_name . '.' . $file_extension;
}

// Prevent overwriting existing files
$target_file = $target_dir . $new_file_name;
$counter = 1;
while (file_exists($target_file)) {
    if ($add_timestamp) {
        $new_file_name = $sanitized_name . '_' . time() . '_' . $counter . '.' . $file_extension;
    } else {
        $new_file_name = $sanitized_name . '_' . $counter . '.' . $file_extension;
    }
    $target_file = $target_dir . $new_file_name;
    $counter++;
}

// Move uploaded file
if (move_uploaded_file($file_tmp, $target_file)) {
    $success_message = "The file " . htmlspecialchars($new_file_name) . " has been uploaded successfully.";
    sendResponse(200, $success_message, $response_format, [
        'filename' => $new_file_name,
        'path' => $target_file,
        'size' => $file_size,
        'type' => $file_type
    ]);
} else {
    $error_message = "Error: There was a problem saving the file. Please try again.";
    sendResponse(500, $error_message, $response_format);
}

/**
 * Send HTTP response in the configured format
 */
function sendResponse($status_code, $message, $format = 'text', $data = []) {
    http_response_code($status_code);
    
    if ($format === 'json') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $status_code === 200,
            'message' => $message,
            'data' => $data
        ]);
    } else {
        header('Content-Type: text/plain');
        echo $message;
    }
}
?>
