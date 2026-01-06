<?php
/**
 * SISMAKUM - Root Index File
 * File ini mengarahkan request ke Frontend (Vue.js) atau Backend (Laravel API)
 */

// Path constants
define('ROOT_PATH', __DIR__);
define('BACKEND_PATH', ROOT_PATH . '/backend');
define('FRONTEND_PATH', ROOT_PATH . '/frontend');
define('FRONTEND_DIST_PATH', FRONTEND_PATH . '/dist');
define('BACKEND_PUBLIC_PATH', BACKEND_PATH . '/public');

// Get request URI
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove query string for path checking
$path = rtrim($requestPath, '/') ?: '/';

// Check if backend exists
if (!file_exists(BACKEND_PUBLIC_PATH . '/index.php')) {
    http_response_code(500);
    die('Backend not found. Please ensure Laravel backend is properly installed.');
}

// Handle API requests - redirect to Laravel backend
if (strpos($path, '/api') === 0) {
    // API request - handle by Laravel
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['REQUEST_URI'] = $requestUri;
    
    // Change to backend public directory
    chdir(BACKEND_PUBLIC_PATH);
    
    // Include Laravel's index.php
    require BACKEND_PUBLIC_PATH . '/index.php';
    exit;
}

// Handle Laravel storage files (if needed)
if (strpos($path, '/storage') === 0) {
    chdir(BACKEND_PUBLIC_PATH);
    require BACKEND_PUBLIC_PATH . '/index.php';
    exit;
}

// Check if frontend dist exists (production build)
$frontendIndex = FRONTEND_DIST_PATH . '/index.html';

if (file_exists($frontendIndex)) {
    // Production: Serve from dist folder
    // Check if it's a static file request
    $requestedFile = FRONTEND_DIST_PATH . $path;
    
    if ($path !== '/' && file_exists($requestedFile) && is_file($requestedFile)) {
        // Serve static file with proper MIME type
        $mimeTypes = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'json' => 'application/json',
            'map' => 'application/json',
        ];
        
        $ext = strtolower(pathinfo($requestedFile, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
        
        // Set cache headers for static files
        header('Content-Type: ' . $mimeType);
        header('Cache-Control: public, max-age=31536000');
        readfile($requestedFile);
        exit;
    }
    
    // Serve Vue.js SPA index.html for all other routes (Vue Router)
    header('Content-Type: text/html; charset=utf-8');
    readfile($frontendIndex);
    exit;
} else {
    // Development: Show message or redirect
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SISMAKUM - Development Mode</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
            }
            .container {
                text-align: center;
                padding: 2rem;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                backdrop-filter: blur(10px);
                max-width: 600px;
            }
            h1 { margin-top: 0; }
            .info {
                background: rgba(255, 255, 255, 0.2);
                padding: 1rem;
                border-radius: 5px;
                margin: 1rem 0;
            }
            code {
                background: rgba(0, 0, 0, 0.3);
                padding: 0.2rem 0.5rem;
                border-radius: 3px;
                font-family: 'Courier New', monospace;
            }
            .links {
                margin-top: 2rem;
            }
            .links a {
                color: #fff;
                text-decoration: none;
                margin: 0 1rem;
                padding: 0.5rem 1rem;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 5px;
                display: inline-block;
            }
            .links a:hover {
                background: rgba(255, 255, 255, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚀 SISMAKUM</h1>
            <p>Sistem Pengusulan PERBUB</p>
            
            <div class="info">
                <h3>Development Mode</h3>
                <p>Frontend belum di-build untuk production.</p>
                <p>Untuk menjalankan aplikasi:</p>
            </div>
            
            <div style="text-align: left; background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 5px; margin: 1rem 0;">
                <strong>Backend (Laravel):</strong><br>
                <code>cd backend && php artisan serve</code><br>
                <small>Berjalan di http://localhost:8000</small>
            </div>
            
            <div style="text-align: left; background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 5px; margin: 1rem 0;">
                <strong>Frontend (Vue.js):</strong><br>
                <code>cd frontend && npm run dev</code><br>
                <small>Berjalan di http://localhost:5173</small>
            </div>
            
            <div style="text-align: left; background: rgba(0,0,0,0.3); padding: 1rem; border-radius: 5px; margin: 1rem 0;">
                <strong>Build untuk Production:</strong><br>
                <code>cd frontend && npm run build</code><br>
                <small>Hasil build akan muncul di <code>frontend/dist/</code></small>
            </div>
            
            <div class="links">
                <a href="/api">Test API</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
