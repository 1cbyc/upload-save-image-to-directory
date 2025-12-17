<?php
/**
 * Image Uploader Configuration
 * 
 * Customize these settings to match your needs.
 * This file is included by uploader.php to configure the upload behavior.
 */

return [
    // Upload directory (relative to this file)
    'upload_directory' => 'uploads/',
    
    // Maximum file size in bytes (default: 5MB)
    'max_file_size' => 5 * 1024 * 1024,
    
    // Allowed MIME types
    'allowed_mime_types' => [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp'
    ],
    
    // Allowed file extensions
    'allowed_extensions' => [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp'
    ],
    
    // Directory permissions (octal)
    'directory_permissions' => 0755,
    
    // Maximum filename length (characters)
    'max_filename_length' => 100,
    
    // Add timestamp to filenames (prevents overwrites)
    'add_timestamp' => true,
    
    // Response format: 'json' or 'text'
    'response_format' => 'text',
    
    // Enable CORS (set to true if uploading from different domain)
    'enable_cors' => false,
    
    // CORS allowed origins (if enable_cors is true)
    'cors_origins' => ['*'],
];
?>

