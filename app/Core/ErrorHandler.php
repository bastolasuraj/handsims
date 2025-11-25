<?php

namespace App\Core;
use ErrorException;
use PDO;
use PDOException;
use Throwable;
use App\Core\Database; // Add this line

class ErrorHandler
{
    /**
     * Register error and exception handlers
     */
    public static function register()
    {
        error_reporting(E_ALL);
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);

        // Hide errors from display in production
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        ini_set('log_errors', 1);
        ini_set('error_log', APP_ROOT . '/logs/error.log');
    }

    /**
     * Handle PHP errors
     */
    public static function handleError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Handle uncaught exceptions
     */
    public static function handleException($exception)
    {
        $code = $exception->getCode();

        // Ensure code is a valid HTTP status code integer
        if (!is_numeric($code) || $code < 100 || $code > 599) {
            $code = 500;
        } else {
            $code = (int)$code;
        }

        // Log the error
        self::logError($exception);

        // Display user-friendly error page
        self::displayErrorPage($code, $exception);
    }

    /**
     * Handle fatal errors
     */
    public static function handleShutdown()
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            self::handleException($exception);
        }
    }

    /**
     * Log error details
     */
    private static function logError($exception)
    {
        $logFile = APP_ROOT . '/logs/error.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $message = sprintf(
            "[%s] %s: %s in %s on line %d\nStack trace:\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        error_log($message, 3, $logFile);
    }

    /**
     * Display user-friendly error page
     */
    private static function displayErrorPage($code, $exception = null)
    {
        // Clean any existing output
        if (ob_get_level() > 0) {
            ob_clean();
        }

        // Ensure code is a valid integer HTTP status code
        if (!is_int($code) || $code < 100 || $code > 599) {
            $code = 500;
        }

        http_response_code($code);

        // Fetch joke line
        $jokeLine = '';
        try {
            $db = Database::getInstance()->getConnection(); // Use fully qualified name
            if ($db) { // Only try to fetch if connection is successful
                $stmt = $db->prepare(
                    "
                    SELECT content 
                    FROM humor_lines 
                    WHERE is_active = 1 
                    ORDER BY RAND() 
                    LIMIT 1
                "
                );
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result && !empty($result['content'])) {
                    $jokeLine = $result['content'];
                }
            }
        } catch (\Exception $e) { // Use global Exception
            // Fallback to JSON file if database fails or connection is not available
            try {
                $jsonPath = APP_ROOT . DIRECTORY_SEPARATOR . 'jokes_and_sarcasms.json';
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
                            $index = random_int(0, count($pool) - 1);
                            $jokeLine = $pool[$index];
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fail silently
            }
        }

        // Determine if we should show detailed errors (development mode)
        $showDetails = (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ||
                       (isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']));

        // For PHP errors in development, use the enhanced error page only for actual exceptions
        if ($showDetails && $exception && $code >= 500) {
            // Determine error level
            $errorLevel = 'error';
            if ($exception instanceof ErrorException) {
                $severity = $exception->getSeverity();
                if ($severity === E_ERROR || $severity === E_CORE_ERROR || $severity === E_COMPILE_ERROR || $severity === E_PARSE) {
                    $errorLevel = 'fatal';
                } elseif ($severity === E_WARNING || $severity === E_CORE_WARNING || $severity === E_COMPILE_WARNING) {
                    $errorLevel = 'warning';
                } elseif ($severity === E_NOTICE) {
                    $errorLevel = 'notice';
                }
            } elseif (strpos(get_class($exception), 'Fatal') !== false) {
                $errorLevel = 'fatal';
            }

            // Use the enhanced PHP error page
            include APP_ROOT . '/app/views/errors/php_error.php';
            exit;
        }

        // Error messages for different codes
        $errorMessages = [
            400 => ['title' => 'Bad Request', 'message' => 'The request could not be understood by the server.'],
            401 => ['title' => 'Unauthorized', 'message' => 'You need to be logged in to access this page.'],
            403 => ['title' => 'Forbidden', 'message' => 'You don\'t have permission to access this resource.'],
            404 => ['title' => 'Page Not Found', 'message' => 'The page you are looking for could not be found.'],
            405 => ['title' => 'Method Not Allowed', 'message' => 'The request method is not supported for this resource.'],
            500 => ['title' => 'Internal Server Error', 'message' => 'Something went wrong on our end. Please try again later.'],
            502 => ['title' => 'Bad Gateway', 'message' => 'The server received an invalid response from the upstream server.'],
            503 => ['title' => 'Service Unavailable', 'message' => 'The server is temporarily unable to handle your request.'],
        ];

        $errorInfo = $errorMessages[$code] ?? $errorMessages[500];

        // Include the error view
        include APP_ROOT . '/app/views/errors/error.php';
        exit;
    }

    /**
     * Display 404 error page
     */
    public static function show404()
    {
        self::displayErrorPage(404);
    }

    /**
     * Display 403 error page
     */
    public static function show403()
    {
        self::displayErrorPage(403);
    }

    /**
     * Display 500 error page
     */
    public static function show500($message = null)
    {
        if ($message) {
            $exception = new \Exception($message);
            self::handleException($exception);
        } else {
            self::displayErrorPage(500);
        }
    }
}
