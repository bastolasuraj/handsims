<?php
// Prevent caching of error pages to ensure fresh humor each time
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}

// Load random humor from database
$jokeLine = '';
try {
    // Try to get database connection - use APP_ROOT if defined, otherwise calculate path
    if (defined('APP_ROOT')) {
        require_once APP_ROOT . '/config/database.php';
    } else {
        // Fallback path calculation: app/views/errors -> go up 3 levels
        $rootPath = dirname(__FILE__, 4); // Goes to h_and_s_inventory_management
        require_once $rootPath . '/config/database.php';
    }
    $db = Database::getInstance()->getConnection();

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
} catch (Exception $e) {
    // Fallback to JSON file if database fails
    try {
        // Use the same root path calculation
        if (defined('APP_ROOT')) {
            $jsonPath = APP_ROOT . DIRECTORY_SEPARATOR . 'jokes_and_sarcasms.json';
        } else {
            $jsonPath = dirname(__FILE__, 4) . DIRECTORY_SEPARATOR . 'jokes_and_sarcasms.json';
        }
        if (is_readable($jsonPath)) {
            $json = file_get_contents($jsonPath);
            $data = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $pool = [];
                if (!empty($data['jokes']) && is_array($data['jokes'])) {
                    $pool = array_merge($pool, $data['jokes']);
                }
                if (!empty($data['sarcasms']) && is_array($data['sarcasms'])) {
                    $pool = array_merge($pool, $data['sarcasms']);
                }
                if (!empty($data['warehouse_specific']) && is_array($data['warehouse_specific'])) {
                    $pool = array_merge($pool, $data['warehouse_specific']);
                }
                if (!empty($pool)) {
                    // Use cryptographically secure random for better randomness
                    $index = random_int(0, count($pool) - 1);
                    $jokeLine = $pool[$index];
                }
            }
        }
    } catch (Throwable $e) {
        // fail silently; no jokes
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $code; ?> - <?php echo htmlspecialchars($errorInfo['title']); ?></title>
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
        
        .error-details {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            text-align: left;
        }
        
        .error-details-title {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .error-details-content {
            background: #f7fafc;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #2d3748;
            overflow-x: auto;
            max-height: 300px;
            overflow-y: auto;
            position: relative;
        }
        
        .copy-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #667eea;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .copy-button:hover {
            background: #764ba2;
            transform: translateY(-1px);
        }
        
        .copy-button.copied {
            background: #48bb78;
        }
        
        .error-line {
            padding: 2px 0;
            line-height: 1.6;
        }
        
        .error-level-fatal {
            color: #e53e3e;
            font-weight: bold;
            background: #fff5f5;
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        .error-level-error {
            color: #dd6b20;
            font-weight: bold;
            background: #fffaf0;
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        .error-level-warning {
            color: #d69e2e;
            font-weight: bold;
            background: #fffff0;
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        .error-level-notice {
            color: #3182ce;
            font-weight: bold;
            background: #ebf8ff;
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        .error-level-info {
            color: #38a169;
            font-weight: bold;
            background: #f0fff4;
            padding: 2px 5px;
            border-radius: 3px;
        }
        
        .stack-frame {
            margin: 5px 0;
            padding: 8px;
            background: white;
            border-left: 3px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .stack-frame:hover {
            background: #edf2f7;
            border-left-color: #667eea;
        }
        
        .stack-frame-number {
            color: #a0aec0;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .stack-frame-file {
            color: #4a5568;
            font-size: 12px;
        }
        
        .stack-frame-function {
            color: #667eea;
            font-weight: bold;
        }
        
        .stack-frame-line {
            color: #e53e3e;
            font-weight: bold;
        }
        
        .error-details-content::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .error-details-content::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 3px;
        }
        
        .error-details-content::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
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
        <!-- Error Icon based on error code -->
        <div class="error-icon">
            <?php if ($code == 404) : ?>
            <!-- 404 Icon - Question mark with X -->
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                <path d="M 70 80 Q 70 60 85 60 Q 100 60 100 80 L 100 110" fill="none" stroke="#667eea" stroke-width="8" stroke-linecap="round"/>
                <circle cx="100" cy="130" r="5" fill="#667eea"/>
                <path d="M 60 70 L 140 130" stroke="#764ba2" stroke-width="6" stroke-linecap="round"/>
                <path d="M 140 70 L 60 130" stroke="#764ba2" stroke-width="6" stroke-linecap="round"/>
            </svg>
            <?php elseif ($code == 403) : ?>
            <!-- 403 Icon - Lock/Shield -->
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                <!-- Lock body -->
                <rect x="70" y="90" width="60" height="45" rx="5" fill="none" stroke="#ef4444" stroke-width="6"/>
                <!-- Lock shackle -->
                <path d="M 80 90 L 80 75 Q 80 55 100 55 Q 120 55 120 75 L 120 90" fill="none" stroke="#ef4444" stroke-width="6" stroke-linecap="round"/>
                <!-- Keyhole -->
                <circle cx="100" cy="105" r="4" fill="#ef4444"/>
                <rect x="98" y="105" width="4" height="15" fill="#ef4444"/>
                <!-- X mark -->
                <path d="M 75 140 L 125 140" stroke="#764ba2" stroke-width="4" stroke-linecap="round"/>
            </svg>
            <?php elseif ($code == 500) : ?>
            <!-- 500 Icon - Broken gear -->
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                <!-- Gear teeth -->
                <path d="M 100 50 L 105 60 L 115 55 L 115 65 L 125 65 L 120 75 L 130 80 L 120 85 L 125 95 L 115 95 L 115 105 L 105 100 L 100 110" 
                      fill="none" stroke="#667eea" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M 100 150 L 95 140 L 85 145 L 85 135 L 75 135 L 80 125 L 70 120 L 80 115 L 75 105 L 85 105 L 85 95 L 95 100 L 100 90" 
                      fill="none" stroke="#667eea" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Lightning bolt (break) -->
                <path d="M 95 70 L 85 100 L 95 100 L 85 130" fill="none" stroke="#ef4444" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M 105 70 L 115 100 L 105 100 L 115 130" fill="none" stroke="#ef4444" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php elseif ($code == 503) : ?>
            <!-- 503 Icon - Maintenance/Wrench -->
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                <!-- Wrench -->
                <path d="M 70 130 L 110 90" stroke="#667eea" stroke-width="8" stroke-linecap="round"/>
                <circle cx="115" cy="85" r="12" fill="none" stroke="#667eea" stroke-width="6"/>
                <circle cx="65" cy="135" r="12" fill="none" stroke="#667eea" stroke-width="6"/>
                <!-- Clock hands (time) -->
                <circle cx="130" cy="130" r="20" fill="none" stroke="#f59e0b" stroke-width="4"/>
                <path d="M 130 130 L 130 115" stroke="#f59e0b" stroke-width="3" stroke-linecap="round"/>
                <path d="M 130 130 L 140 135" stroke="#f59e0b" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <?php else : ?>
            <!-- Generic Error Icon -->
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                <path d="M 100 50 L 100 110" stroke="#667eea" stroke-width="10" stroke-linecap="round"/>
                <circle cx="100" cy="140" r="6" fill="#764ba2"/>
            </svg>
            <?php endif; ?>
        </div>
        
        <div class="error-code"><?php echo $code; ?></div>
        <h1 class="error-title">
            <?php if ($code == 404) : ?>
                😆 AWWW... Did you get lost... 😆
            <?php elseif ($code == 403) : ?>
                🚫 Access Denied! No PPE, No Entry! 🚫
            <?php elseif ($code == 500) : ?>
                💥 Oops! Something Broke... 💥
            <?php elseif ($code == 503) : ?>
                🔧 Under Maintenance... Back Soon! 🔧
            <?php else : ?>
                <?php echo htmlspecialchars($errorInfo['title']); ?>
            <?php endif; ?>
        </h1>

        <!-- Display random joke as the error message -->
        <p class="error-message">
            💭 <?php
            // Display random joke instead of the standard error message
            if (!empty($jokeLine)) {
                echo htmlspecialchars($jokeLine);
            } else {
                // Fallback to default message if no joke is available
                echo htmlspecialchars($errorInfo['message']);
            }
            ?>
        </p>

        <div class="action-buttons" style="margin-bottom: 30px;">
            <?php if ($code != 404) : ?>
            <a href="javascript:history.back()" class="btn btn-secondary">
                ← Go Back
            </a>
            <?php endif; ?>
            <a href="<?php echo defined('APP_URL') ? APP_URL : '/'; ?>" class="btn btn-primary">
                Go to Homepage
            </a>
        </div>
        
        <?php if ($code >= 500) : ?>
        <div class="suggestion-box">
            <h4>🔧 What you can try:</h4>
            <ul>
                <li>Refresh the page in a few moments</li>
                <li>Clear your browser cache and cookies</li>
                <li>Contact support if the problem persists</li>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if (isset($showDetails) && $showDetails && isset($exception)) : ?>
        <div class="error-details">
            <div class="error-details-title">Technical Details (Development Mode)</div>
            <div class="error-details-content" id="errorDetails">
                <button class="copy-button" onclick="copyErrorDetails()" id="copyBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span id="copyBtnText">Copy</span>
                </button>
                
                <?php
                $errorLevel = 'error';
                if (strpos(get_class($exception), 'Fatal') !== false) {
                    $errorLevel = 'fatal';
                } elseif (strpos(get_class($exception), 'Warning') !== false) {
                    $errorLevel = 'warning';
                } elseif (strpos(get_class($exception), 'Notice') !== false) {
                    $errorLevel = 'notice';
                }
                ?>
                
                <div class="error-line">
                    <span class="error-level-<?php echo $errorLevel; ?>">
                        <?php echo strtoupper($errorLevel); ?>
                    </span>
                    <strong>Exception:</strong> <?php echo get_class($exception); ?>
                </div>
                <div class="error-line">
                    <strong>Message:</strong> <?php echo htmlspecialchars($exception->getMessage()); ?>
                </div>
                <div class="error-line">
                    <strong>File:</strong> <span class="stack-frame-file"><?php echo htmlspecialchars($exception->getFile()); ?></span>
                </div>
                <div class="error-line">
                    <strong>Line:</strong> <span class="stack-frame-line"><?php echo $exception->getLine(); ?></span>
                </div>
                
                <div style="margin-top: 15px;">
                    <strong>Stack Trace:</strong>
                    <?php
                    $trace = $exception->getTrace();
                    foreach ($trace as $index => $frame) :
                        $file = isset($frame['file']) ? $frame['file'] : 'unknown';
                        $line = isset($frame['line']) ? $frame['line'] : '0';
                        $function = isset($frame['function']) ? $frame['function'] : 'unknown';
                        $class = isset($frame['class']) ? $frame['class'] . $frame['type'] : '';
                        ?>
                    <div class="stack-frame">
                        <span class="stack-frame-number">#<?php echo $index; ?></span>
                        <span class="stack-frame-function"><?php echo htmlspecialchars($class . $function); ?>()</span>
                        <div class="stack-frame-file">
                            <?php echo htmlspecialchars($file); ?>:<span class="stack-frame-line"><?php echo $line; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    function copyErrorDetails() {
        const errorDetails = document.getElementById('errorDetails');
        const copyBtn = document.getElementById('copyBtn');
        const copyBtnText = document.getElementById('copyBtnText');
        
        // Get text content without HTML
        const textContent = errorDetails.innerText || errorDetails.textContent;
        
        // Remove the "Copy" button text from the content
        const cleanText = textContent.replace(/Copy|Copied!/g, '').trim();
        
        // Create a temporary textarea to copy the text
        const textarea = document.createElement('textarea');
        textarea.value = cleanText;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        
        // Select and copy the text
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        // Update button to show success
        copyBtn.classList.add('copied');
        copyBtnText.textContent = 'Copied!';
        
        // Reset button after 2 seconds
        setTimeout(() => {
            copyBtn.classList.remove('copied');
            copyBtnText.textContent = 'Copy';
        }, 2000);
    }
    </script>
</body>
</html>
