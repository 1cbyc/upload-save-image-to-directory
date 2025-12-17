<?php
/**
 * Setup Helper for Image Uploader Tool
 * 
 * Run this file once to verify your configuration and create necessary directories.
 * Access via: http://your-domain.com/setup.php
 */

// Security: Only allow access in development (remove or restrict in production)
$allow_setup = true; // Set to false in production

if (!$allow_setup) {
    die('Setup is disabled.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Uploader - Setup</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 20px; }
        .check { color: #28a745; margin: 10px 0; }
        .error { color: #dc3545; margin: 10px 0; }
        .info { color: #17a2b8; margin: 10px 0; }
        .section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Image Uploader Setup</h1>
        
        <?php
        $errors = [];
        $warnings = [];
        $success = [];
        
        // Check if config.php exists
        if (file_exists('config.php')) {
            $success[] = "Configuration file (config.php) exists";
            $config = require 'config.php';
        } else {
            $errors[] = "Configuration file (config.php) not found. Please copy config.example.php to config.php";
        }
        
        // Check upload directory
        if (isset($config)) {
            $upload_dir = $config['upload_directory'];
            
            if (file_exists($upload_dir)) {
                $success[] = "Upload directory exists: <code>$upload_dir</code>";
                
                if (!is_writable($upload_dir)) {
                    $errors[] = "Upload directory is not writable: <code>$upload_dir</code>";
                } else {
                    $success[] = "Upload directory is writable";
                }
            } else {
                // Try to create it
                if (mkdir($upload_dir, $config['directory_permissions'] ?? 0755, true)) {
                    $success[] = "Created upload directory: <code>$upload_dir</code>";
                } else {
                    $errors[] = "Could not create upload directory: <code>$upload_dir</code>";
                }
            }
            
            // Display configuration
            echo '<div class="section">';
            echo '<h3>Current Configuration:</h3>';
            echo '<ul>';
            echo '<li><strong>Upload Directory:</strong> <code>' . htmlspecialchars($upload_dir) . '</code></li>';
            echo '<li><strong>Max File Size:</strong> ' . round($config['max_file_size'] / (1024 * 1024), 2) . ' MB</li>';
            echo '<li><strong>Allowed Extensions:</strong> ' . implode(', ', $config['allowed_extensions']) . '</li>';
            echo '<li><strong>Response Format:</strong> ' . $config['response_format'] . '</li>';
            echo '<li><strong>CORS Enabled:</strong> ' . ($config['enable_cors'] ? 'Yes' : 'No') . '</li>';
            echo '</ul>';
            echo '</div>';
        }
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
            $success[] = "PHP version is compatible: " . PHP_VERSION;
        } else {
            $warnings[] = "PHP version " . PHP_VERSION . " is below recommended 7.0.0";
        }
        
        // Check required PHP functions
        $required_functions = ['move_uploaded_file', 'getimagesize', 'mkdir'];
        foreach ($required_functions as $func) {
            if (function_exists($func)) {
                $success[] = "PHP function <code>$func</code> is available";
            } else {
                $errors[] = "Required PHP function <code>$func</code> is not available";
            }
        }
        
        // Display results
        if (!empty($success)) {
            echo '<div class="section"><h3>✅ Checks Passed:</h3><ul>';
            foreach ($success as $msg) {
                echo '<li class="check">' . $msg . '</li>';
            }
            echo '</ul></div>';
        }
        
        if (!empty($warnings)) {
            echo '<div class="section"><h3>⚠️ Warnings:</h3><ul>';
            foreach ($warnings as $msg) {
                echo '<li class="error">' . $msg . '</li>';
            }
            echo '</ul></div>';
        }
        
        if (!empty($errors)) {
            echo '<div class="section"><h3>❌ Errors:</h3><ul>';
            foreach ($errors as $msg) {
                echo '<li class="error">' . $msg . '</li>';
            }
            echo '</ul></div>';
        }
        
        if (empty($errors)) {
            echo '<div class="section">';
            echo '<p class="check"><strong>Setup complete! Your image uploader is ready to use.</strong></p>';
            echo '<p><a href="index.html">Go to Upload Page →</a></p>';
            echo '</div>';
        }
        ?>
        
        <div class="section">
            <h3>📝 Next Steps:</h3>
            <ol>
                <li>Review and customize <code>config.php</code> if needed</li>
                <li>Update the CONFIG object in <code>index.html</code> to match your config.php settings</li>
                <li>Test the upload functionality</li>
                <li><strong>Important:</strong> Disable this setup.php file in production by setting <code>$allow_setup = false;</code></li>
            </ol>
        </div>
    </div>
</body>
</html>

