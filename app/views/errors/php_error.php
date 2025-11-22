<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Error - H&S Inventory Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            background: linear-gradient(135deg, #1e1e2e 0%, #2d2d44 100%);
            min-height: 100vh;
            color: #e0e0e0;
            padding: 20px;
            overflow-x: hidden;
        }
        
        .error-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border-radius: 12px 12px 0 0;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .error-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .error-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #2a2a3e;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }
        
        .error-type {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 1;
        }
        
        .error-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-title {
            font-size: 28px;
            font-weight: 700;
            margin: 20px 0 10px;
            color: #fff;
            position: relative;
            z-index: 1;
        }
        
        .error-message {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            position: relative;
            z-index: 1;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #ffd93d;
        }
        
        .error-location {
            background: #1e1e2e;
            padding: 20px 30px;
            border-left: 4px solid #6c5ce7;
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .location-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(108, 92, 231, 0.1);
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid rgba(108, 92, 231, 0.3);
        }
        
        .location-label {
            color: #a0a0a0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .location-value {
            color: #6c5ce7;
            font-weight: 600;
            font-size: 14px;
        }
        
        .tabs {
            display: flex;
            background: #1e1e2e;
            padding: 0 20px;
            gap: 5px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .tab {
            padding: 15px 25px;
            background: transparent;
            border: none;
            color: #a0a0a0;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
            position: relative;
            font-family: inherit;
        }
        
        .tab:hover {
            color: #fff;
            background: rgba(108, 92, 231, 0.1);
        }
        
        .tab.active {
            background: #2a2a3e;
            color: #6c5ce7;
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #6c5ce7;
        }
        
        .tab-content {
            display: none;
            padding: 30px;
            animation: fadeIn 0.3s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stack-trace {
            background: #1e1e2e;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .stack-frame {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .stack-frame:hover {
            background: rgba(108, 92, 231, 0.05);
            padding-left: 30px;
        }
        
        .stack-frame.highlight {
            background: rgba(255, 107, 107, 0.1);
            border-left: 4px solid #ff6b6b;
        }
        
        .frame-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: rgba(108, 92, 231, 0.2);
            color: #6c5ce7;
            text-align: center;
            line-height: 30px;
            border-radius: 50%;
            font-weight: 600;
            font-size: 12px;
            margin-right: 15px;
        }
        
        .frame-function {
            color: #ffd93d;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 5px;
        }
        
        .frame-location {
            color: #a0a0a0;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 5px;
        }
        
        .frame-file {
            color: #6c5ce7;
        }
        
        .frame-line {
            background: rgba(255, 107, 107, 0.2);
            color: #ff6b6b;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .code-preview {
            background: #1a1a2e;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            position: relative;
            overflow-x: auto;
        }
        
        .code-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .code-title {
            color: #6c5ce7;
            font-size: 14px;
            font-weight: 600;
        }
        
        .copy-button {
            background: rgba(108, 92, 231, 0.2);
            border: 1px solid rgba(108, 92, 231, 0.3);
            color: #6c5ce7;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .copy-button:hover {
            background: rgba(108, 92, 231, 0.3);
            transform: translateY(-1px);
        }
        
        .code-lines {
            font-size: 14px;
            line-height: 1.8;
        }
        
        .code-line {
            display: flex;
            padding: 2px 0;
        }
        
        .code-line:hover {
            background: rgba(108, 92, 231, 0.05);
        }
        
        .code-line.error-line {
            background: rgba(255, 107, 107, 0.1);
            border-left: 3px solid #ff6b6b;
            padding-left: 17px;
        }
        
        .line-number {
            width: 50px;
            color: #606060;
            text-align: right;
            padding-right: 20px;
            user-select: none;
        }
        
        .line-content {
            flex: 1;
            color: #e0e0e0;
            white-space: pre;
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
        }
        
        .context-vars {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .var-card {
            background: #1e1e2e;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .var-name {
            color: #6c5ce7;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .var-value {
            background: #1a1a2e;
            padding: 10px;
            border-radius: 6px;
            color: #ffd93d;
            font-size: 13px;
            overflow-x: auto;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .var-value::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .var-value::-webkit-scrollbar-track {
            background: #1a1a2e;
        }
        
        .var-value::-webkit-scrollbar-thumb {
            background: #6c5ce7;
            border-radius: 3px;
        }
        
        .suggestions {
            background: linear-gradient(135deg, #48c774 0%, #3ec46d 100%);
            padding: 20px 30px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .suggestions h3 {
            color: #fff;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .suggestion-list {
            list-style: none;
        }
        
        .suggestion-list li {
            color: rgba(255, 255, 255, 0.95);
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            line-height: 1.6;
        }
        
        .suggestion-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #fff;
            font-weight: bold;
        }
        
        .actions {
            padding: 30px;
            background: #1e1e2e;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6c5ce7 0%, #5f4dd6 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 92, 231, 0.3);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        .search-box {
            background: #1e1e2e;
            border: 1px solid rgba(108, 92, 231, 0.3);
            border-radius: 8px;
            padding: 10px 15px;
            color: #e0e0e0;
            font-size: 14px;
            width: 100%;
            margin-bottom: 20px;
            font-family: inherit;
        }
        
        .search-box:focus {
            outline: none;
            border-color: #6c5ce7;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-card {
            background: #1e1e2e;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .info-label {
            color: #a0a0a0;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #6c5ce7;
            font-size: 16px;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .container {
                border-radius: 0;
            }
            
            .tabs {
                overflow-x: auto;
                padding: 0 10px;
            }
            
            .tab {
                white-space: nowrap;
                padding: 12px 15px;
            }
            
            .error-location {
                padding: 15px;
            }
            
            .location-item {
                width: 100%;
            }
            
            .actions {
                padding: 20px;
            }
            
            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Error Header -->
        <div class="error-header">
            <div class="error-type">
                <div class="error-icon">⚠️</div>
                <span><?php echo isset($errorLevel) ? strtoupper($errorLevel) : 'ERROR'; ?></span>
            </div>
            <h1 class="error-title">
                <?php echo isset($exception) ? get_class($exception) : 'System Error'; ?>
            </h1>
            <div class="error-message">
                <?php echo isset($exception) ? htmlspecialchars($exception->getMessage()) : 'An unexpected error occurred'; ?>
            </div>
        </div>
        
        <!-- Error Location -->
        <div class="error-location">
            <div class="location-item">
                <span class="location-label">File</span>
                <span class="location-value"><?php echo isset($exception) ? basename($exception->getFile()) : 'Unknown'; ?></span>
            </div>
            <div class="location-item">
                <span class="location-label">Line</span>
                <span class="location-value"><?php echo isset($exception) ? $exception->getLine() : '0'; ?></span>
            </div>
            <div class="location-item">
                <span class="location-label">Time</span>
                <span class="location-value"><?php echo date('H:i:s'); ?></span>
            </div>
            <div class="location-item">
                <span class="location-label">PHP</span>
                <span class="location-value"><?php echo PHP_VERSION; ?></span>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="showTab('stack')">
                📚 Stack Trace
            </button>
            <button class="tab" onclick="showTab('code')">
                📝 Source Code
            </button>
            <button class="tab" onclick="showTab('context')">
                🔍 Context
            </button>
            <button class="tab" onclick="showTab('request')">
                🌐 Request
            </button>
            <button class="tab" onclick="showTab('environment')">
                ⚙️ Environment
            </button>
        </div>
        
        <!-- Tab Contents -->
        <div id="stack" class="tab-content active">
            <input type="text" class="search-box" placeholder="🔍 Search in stack trace..." onkeyup="searchStack(this.value)">
            <div class="stack-trace">
                <?php if (isset($exception) && method_exists($exception, 'getTrace')) : ?>
                    <?php foreach ($exception->getTrace() as $index => $frame) : ?>
                        <div class="stack-frame <?php echo $index === 0 ? 'highlight' : ''; ?>">
                            <div>
                                <span class="frame-number"><?php echo $index + 1; ?></span>
                                <span class="frame-function">
                                    <?php
                                    $function = '';
                                    if (isset($frame['class'])) {
                                        $function .= $frame['class'] . $frame['type'];
                                    }
                                    $function .= $frame['function'] ?? 'unknown';
                                    echo htmlspecialchars($function) . '()';
                                    ?>
                                </span>
                            </div>
                            <div class="frame-location">
                                <span class="frame-file">
                                    <?php echo isset($frame['file']) ? htmlspecialchars($frame['file']) : 'unknown'; ?>
                                </span>
                                <?php if (isset($frame['line'])) : ?>
                                    <span class="frame-line">Line <?php echo $frame['line']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="stack-frame">
                        <span>No stack trace available</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div id="code" class="tab-content">
            <div class="code-preview">
                <div class="code-header">
                    <span class="code-title">
                        📁 <?php echo isset($exception) ? htmlspecialchars($exception->getFile()) : 'Source unavailable'; ?>
                    </span>
                    <button class="copy-button" onclick="copyCode()">📋 Copy</button>
                </div>
                <div class="code-lines">
                    <?php
                    if (isset($exception) && file_exists($exception->getFile())) {
                        $lines = file($exception->getFile());
                        $errorLine = $exception->getLine();
                        $start = max(0, $errorLine - 10);
                        $end = min(count($lines), $errorLine + 10);

                        for ($i = $start; $i < $end; $i++) {
                            $lineNum = $i + 1;
                            $isErrorLine = $lineNum === $errorLine;
                            echo '<div class="code-line ' . ($isErrorLine ? 'error-line' : '') . '">';
                            echo '<span class="line-number">' . $lineNum . '</span>';
                            echo '<span class="line-content">' . htmlspecialchars($lines[$i]) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="code-line"><span class="line-content">Source code not available</span></div>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="suggestions">
                <h3>💡 Possible Solutions</h3>
                <ul class="suggestion-list">
                    <?php if (isset($exception)) : ?>
                        <?php if (strpos($exception->getMessage(), 'undefined') !== false) : ?>
                            <li>Check if the variable or function is properly defined</li>
                            <li>Verify the spelling and case sensitivity</li>
                            <li>Ensure required files are included</li>
                        <?php elseif (strpos($exception->getMessage(), 'database') !== false || strpos($exception->getMessage(), 'SQL') !== false) : ?>
                            <li>Check database connection settings</li>
                            <li>Verify SQL syntax and table names</li>
                            <li>Ensure database server is running</li>
                        <?php elseif (strpos($exception->getMessage(), 'permission') !== false) : ?>
                            <li>Check file and directory permissions</li>
                            <li>Verify user has necessary access rights</li>
                            <li>Review security settings</li>
                        <?php else : ?>
                            <li>Review the error message and stack trace</li>
                            <li>Check recent code changes</li>
                            <li>Verify all dependencies are installed</li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li>Clear application cache if applicable</li>
                    <li>Check error logs for additional information</li>
                </ul>
            </div>
        </div>
        
        <div id="context" class="tab-content">
            <h3 style="color: #6c5ce7; margin-bottom: 20px;">🔍 Variables in Scope</h3>
            <div class="context-vars">
                <?php
                $definedVars = get_defined_vars();
                $skipVars = ['GLOBALS', '_GET', '_POST', '_COOKIE', '_FILES', '_SERVER', '_ENV', '_REQUEST', '_SESSION'];
                foreach ($definedVars as $varName => $varValue) {
                    if (!in_array($varName, $skipVars) && $varName !== 'definedVars' && $varName !== 'skipVars') {
                        echo '<div class="var-card">';
                        echo '<div class="var-name">$' . htmlspecialchars($varName) . '</div>';
                        echo '<div class="var-value">';
                        if (is_array($varValue) || is_object($varValue)) {
                            echo '<pre>' . htmlspecialchars(print_r($varValue, true)) . '</pre>';
                        } else {
                            echo htmlspecialchars(var_export($varValue, true));
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
        
        <div id="request" class="tab-content">
            <h3 style="color: #6c5ce7; margin-bottom: 20px;">🌐 Request Information</h3>
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Method</div>
                    <div class="info-value"><?php echo $_SERVER['REQUEST_METHOD'] ?? 'N/A'; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">URI</div>
                    <div class="info-value"><?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">IP Address</div>
                    <div class="info-value"><?php echo $_SERVER['REMOTE_ADDR'] ?? 'N/A'; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">User Agent</div>
                    <div class="info-value" style="font-size: 12px;"><?php echo $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'; ?></div>
                </div>
            </div>
            
            <?php if (!empty($_GET)) : ?>
                <h4 style="color: #ffd93d; margin: 20px 0 10px;">GET Parameters</h4>
                <div class="var-value">
                    <pre><?php echo htmlspecialchars(print_r($_GET, true)); ?></pre>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($_POST)) : ?>
                <h4 style="color: #ffd93d; margin: 20px 0 10px;">POST Data</h4>
                <div class="var-value">
                    <pre><?php echo htmlspecialchars(print_r($_POST, true)); ?></pre>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($_SESSION)) : ?>
                <h4 style="color: #ffd93d; margin: 20px 0 10px;">Session Data</h4>
                <div class="var-value">
                    <pre><?php echo htmlspecialchars(print_r($_SESSION, true)); ?></pre>
                </div>
            <?php endif; ?>
        </div>
        
        <div id="environment" class="tab-content">
            <h3 style="color: #6c5ce7; margin-bottom: 20px;">⚙️ Environment Details</h3>
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">PHP Version</div>
                    <div class="info-value"><?php echo PHP_VERSION; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Server Software</div>
                    <div class="info-value"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Operating System</div>
                    <div class="info-value"><?php echo PHP_OS; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Memory Usage</div>
                    <div class="info-value"><?php echo round(memory_get_usage() / 1024 / 1024, 2) . ' MB'; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Memory Peak</div>
                    <div class="info-value"><?php echo round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB'; ?></div>
                </div>
                <div class="info-card">
                    <div class="info-label">Execution Time</div>
                    <div class="info-value"><?php echo round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4) . 's'; ?></div>
                </div>
            </div>
            
            <h4 style="color: #ffd93d; margin: 20px 0 10px;">Loaded Extensions</h4>
            <div class="var-value">
                <?php
                $extensions = get_loaded_extensions();
                echo implode(', ', $extensions);
                ?>
            </div>
            
            <h4 style="color: #ffd93d; margin: 20px 0 10px;">Include Path</h4>
            <div class="var-value">
                <?php echo htmlspecialchars(get_include_path()); ?>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="actions">
            <button onclick="window.location.reload()" class="action-btn btn-secondary">
                🔄 Retry Request
            </button>
            <a href="<?php echo defined('APP_URL') ? APP_URL : '/'; ?>" class="action-btn btn-primary">
                🏠 Go to Homepage
            </a>
            <button onclick="reportError()" class="action-btn btn-secondary">
                📧 Report Issue
            </button>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        function searchStack(query) {
            const frames = document.querySelectorAll('.stack-frame');
            frames.forEach(frame => {
                const text = frame.textContent.toLowerCase();
                if (query && !text.includes(query.toLowerCase())) {
                    frame.style.display = 'none';
                } else {
                    frame.style.display = 'block';
                }
            });
        }
        
        function copyCode() {
            const codeLines = document.querySelectorAll('.line-content');
            let code = '';
            codeLines.forEach(line => {
                code += line.textContent;
            });
            
            navigator.clipboard.writeText(code).then(() => {
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '✅ Copied!';
                setTimeout(() => {
                    button.textContent = originalText;
                }, 2000);
            });
        }
        
        function reportError() {
            const errorData = {
                error: '<?php echo isset($exception) ? addslashes(get_class($exception)) : "Unknown"; ?>',
                message: '<?php echo isset($exception) ? addslashes($exception->getMessage()) : ""; ?>',
                file: '<?php echo isset($exception) ? addslashes($exception->getFile()) : ""; ?>',
                line: '<?php echo isset($exception) ? $exception->getLine() : 0; ?>',
                time: '<?php echo date("Y-m-d H:i:s"); ?>'
            };
            
            // You can implement actual error reporting here
            alert('Error report prepared. In production, this would be sent to your error tracking system.');
            console.log('Error Report:', errorData);
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key >= '1' && e.key <= '5') {
                const tabs = ['stack', 'code', 'context', 'request', 'environment'];
                const tabIndex = parseInt(e.key) - 1;
                if (tabs[tabIndex]) {
                    document.querySelectorAll('.tab')[tabIndex].click();
                }
            }
        });
    </script>
</body>
</html>
