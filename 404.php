<?php
// 404 Error Page with Health & Safety Humor
$code = 404;
$errorInfo = [
    'title' => 'Page Not Found',
    'message' => 'The page you are looking for does not exist.'
];
$showDetails = false;

// Load random humor from database or JSON
$jokeLine = '';

// First try database
try {
    if (file_exists('config/config.php') && file_exists('config/database.php')) {
        require_once 'config/config.php';
        require_once 'config/database.php';
        $db = Database::getInstance()->getConnection();
        
        // Check if humor_lines table exists and has data
        $tableCheck = $db->query("SHOW TABLES LIKE 'humor_lines'")->fetch();
        if ($tableCheck) {
            // Fetch a random active humor line
            $stmt = $db->prepare("
                SELECT content 
                FROM humor_lines 
                WHERE is_active = 1 
                ORDER BY RAND() 
                LIMIT 1
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['content'])) {
                $jokeLine = $result['content'];
            }
        }
    }
} catch (Exception $e) {
    // Database failed, will try JSON fallback
}

// If no joke from database, try JSON file
if (empty($jokeLine)) {
    try {
        $jsonPath = __DIR__ . '/jokes_and_sarcasms.json';
        if (file_exists($jsonPath) && is_readable($jsonPath)) {
            $json = file_get_contents($jsonPath);
            $data = json_decode($json, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $pool = [];
                
                // Collect all jokes from all categories
                foreach (['jokes', 'sarcasms', 'warehouse_specific', 'it_specific'] as $category) {
                    if (!empty($data[$category]) && is_array($data[$category])) {
                        $pool = array_merge($pool, $data[$category]);
                    }
                }
                
                // Pick a random joke
                if (!empty($pool)) {
                    $index = array_rand($pool);
                    $jokeLine = $pool[$index];
                }
            }
        }
    } catch (Throwable $e) {
        // JSON failed too
    }
}

// If still no joke, use a random default health & safety joke
if (empty($jokeLine)) {
    $defaultJokes = [
        "Safety first! This page forgot to do its hazard assessment before loading.",
        "PPE Check: Hard hat ✓, Safety vest ✓, Steel-toed boots ✓, Webpage ✗",
        "This page didn't attend the mandatory safety meeting. Access denied!",
        "Error: Page failed to complete its Job Safety Analysis form.",
        "This page is on workers' comp. Slipped on some bad code.",
        "OSHA called - even they can't find this page.",
        "This page violated the two-person lift policy and threw out its back.",
        "Lock Out, Tag Out procedure activated. This page is under maintenance.",
        "Incident Report: Page went missing without signing out. Last seen near the server room.",
        "This page is in the safety shower. It'll be back in 15 minutes."
    ];
    $jokeLine = $defaultJokes[array_rand($defaultJokes)];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .error-code {
            font-size: 120px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .error-title {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .error-message {
            font-size: 18px;
            color: #640a22f7;
            font-weight: bolder;
            line-height: 1.6;
            margin-bottom: 40px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 10px;
            border-left: 4px solid #640a22f7;
        }
        
        .error-icon {
            width: 150px;
            height: 150px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .error-icon svg {
            width: 100%;
            height: 100%;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
        }
        
        .suggestion-box {
            background: #fef5e7;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin-top: 30px;
            border-radius: 8px;
            text-align: left;
        }
        
        .suggestion-box h4 {
            color: #e67e22;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .suggestion-box ul {
            color: #7f8c8d;
            margin-left: 20px;
            line-height: 1.8;
        }
        
        @media (max-width: 640px) {
            .error-container {
                padding: 40px 20px;
            }
            
            .error-code {
                font-size: 80px;
            }
            
            .error-title {
                font-size: 24px;
            }
            
            .error-message {
                font-size: 16px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <!-- 404 Icon -->
        <div class="error-icon">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                <path d="M 70 80 Q 70 60 85 60 Q 100 60 100 80 L 100 110" fill="none" stroke="#667eea" stroke-width="8" stroke-linecap="round"/>
                <circle cx="100" cy="130" r="5" fill="#667eea"/>
                <path d="M 60 70 L 140 130" stroke="#764ba2" stroke-width="6" stroke-linecap="round"/>
                <path d="M 140 70 L 60 130" stroke="#764ba2" stroke-width="6" stroke-linecap="round"/>
            </svg>
        </div>
        
        <div class="error-code">404</div>
        <h1 class="error-title">😆 AWWW... Did you get lost... 😆</h1>
        
        <!-- Display random health & safety joke as the error message -->
        <p class="error-message">
            💭 <?php echo htmlspecialchars($jokeLine); ?>
        </p>
        
        <div class="action-buttons">
            <a href="<?php 
                if (defined('APP_URL')) {
                    echo APP_URL;
                } else {
                    // Fallback to dynamic detection if APP_URL not defined
                    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $path = dirname($_SERVER['SCRIPT_NAME']);
                    echo $protocol . '://' . $host . $path;
                }
            ?>" class="btn btn-primary">
                Go to Homepage
            </a>
        </div>
        
    </div>
</body>
</html>