<?php
// Configuration
$target_dir = "uploads/";
$max_file_size = 5 * 1024 * 1024; // 5MB
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Create uploads directory if it doesn't exist
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Check if file was uploaded
if (!isset($_FILES["image"]) || $_FILES["image"]["error"] != UPLOAD_ERR_OK) {
    http_response_code(400);
    echo "Error: No file uploaded or upload error occurred.";
    exit;
}

$file = $_FILES["image"];
$file_name = $file["name"];
$file_tmp = $file["tmp_name"];
$file_size = $file["size"];
$file_type = $file["type"];

// Validate file size
if ($file_size > $max_file_size) {
    http_response_code(400);
    echo "Error: File size exceeds the maximum limit of 5MB.";
    exit;
}

// Validate file type by MIME type
if (!in_array(strtolower($file_type), $allowed_types)) {
    http_response_code(400);
    echo "Error: Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed.";
    exit;
}

// Get file extension
$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Validate file extension
if (!in_array($file_extension, $allowed_extensions)) {
    http_response_code(400);
    echo "Error: Invalid file extension. Only JPG, PNG, GIF, and WEBP images are allowed.";
    exit;
}

// Additional security: Verify it's actually an image using getimagesize
$image_info = @getimagesize($file_tmp);
if ($image_info === false) {
    http_response_code(400);
    echo "Error: File is not a valid image.";
    exit;
}

// Sanitize filename
$sanitized_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", pathinfo($file_name, PATHINFO_FILENAME));
$sanitized_name = substr($sanitized_name, 0, 100); // Limit filename length
$new_file_name = $sanitized_name . '_' . time() . '.' . $file_extension;

// Prevent overwriting existing files
$target_file = $target_dir . $new_file_name;
$counter = 1;
while (file_exists($target_file)) {
    $new_file_name = $sanitized_name . '_' . time() . '_' . $counter . '.' . $file_extension;
    $target_file = $target_dir . $new_file_name;
    $counter++;
}

// Move uploaded file
if (move_uploaded_file($file_tmp, $target_file)) {
    http_response_code(200);
    echo "The file " . htmlspecialchars($new_file_name) . " has been uploaded successfully.";
} else {
    http_response_code(500);
    echo "Error: There was a problem saving the file. Please try again.";
}
?>
