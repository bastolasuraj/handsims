<?php

namespace App\Controllers;

use App\Core\Controller;
use Exception;
use App\Helpers\ActiveDirectory;

class AuthController extends Controller
{
    private function validateLoginData($data)
    {
        $errors = [];
        if (empty($data['username'])) {
            $errors[] = "Username is required.";
        } elseif (strlen($data['username']) > 50) {
            $errors[] = "Username cannot exceed 50 characters.";
        }

        if (empty($data['password'])) {
            $errors[] = "Password is required.";
        } elseif (strlen($data['password']) > 255) {
            // Passwords will be hashed, so this is for raw input max length
            $errors[] = "Password cannot exceed 255 characters.";
        }

        return $errors;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $loginData = [
                'username' => $username,
                'password' => $password
            ];
            $errors = $this->validateLoginData($loginData);
            if (!empty($errors)) {
                $data = [
                    'error' => implode(' ', $errors),
                    'username' => $username,
                    'ldap_status' => $this->checkLDAPExtension() // Re-check LDAP status for error display
                ];
                $this->view('auth/login', $data, false);
                return;
            }

            try {
                // Check if LDAP is available
                $ldapStatus = $this->checkLDAPExtension();
                $adUser = false;
                error_log("LDAP Status: " . ($ldapStatus['enabled'] ? 'Enabled' : 'Disabled'));
                if ($ldapStatus['enabled']) {
                    // Try AD authentication if LDAP is enabled
                    $ad = new ActiveDirectory();
                    error_log("AD Object Created, isEnabled: " . ($ad->isEnabled() ? 'Yes' : 'No'));
                    if ($ad->isEnabled()) {
                        error_log("Attempting AD authentication for user: " . $username);
                        $adUser = $ad->authenticate($username, $password);
                        error_log("AD authentication result: " . ($adUser ? 'Success' : 'Failed'));
                    } else {
                        error_log("AD is not enabled - check LDAP connection");
                    }
                } else {
                    error_log("LDAP extension is not enabled");
                }

                if ($adUser) {
                    // Check if user exists in local database
                    $userModel = $this->model('UserModel');
                    $localUser = $userModel->getByUsername($username);
                    // Resolve department name to ID
                    $departmentId = $userModel->getDepartmentIdByName($adUser['department']);
                    if (!$localUser) {
                        // Create user in local database if doesn't exist
                        $userData = [
                            'username' => $adUser['username'],
                            'email' => $adUser['email'],
                            'full_name' => $adUser['full_name'],
                            'department_id' => $departmentId, // Use department_id
                            'auth_type' => 'AD'
                        ];
                        $userId = $userModel->createFromAD($userData);
                        $localUser = $userModel->getById($userId);
                    } else {
                        // Update user info from AD
                        $userModel->updateFromAD(
                            $localUser['id'], [
                            'email' => $adUser['email'],
                            'full_name' => $adUser['full_name'],
                            'department_id' => $departmentId // Use department_id
                            ]
                        );
                    }

                    // Set session variables
                    $_SESSION['user_id'] = $localUser['id'];
                    $_SESSION['username'] = $localUser['username'];
                    $_SESSION['role'] = $localUser['role'];
                    $_SESSION['full_name'] = $adUser['full_name'];
                    $_SESSION['auth_type'] = 'AD';
                    $this->addLog("User logged in via AD", "Username: " . $username);
                    // Check for returnTo parameter
                    $returnTo = $_GET['returnTo'] ?? $_POST['returnTo'] ?? '/dashboard';
                    $this->redirect($returnTo);
                } else {
                    // If AD auth fails or is not available, try local authentication
                    $userModel = $this->model('UserModel');
                    $user = $userModel->authenticate($username, $password);
                    if ($user && $user['auth_type'] === 'local') {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['auth_type'] = 'local';
                        $this->addLog("User logged in (local)", "Username: " . $username);
                        // Check for returnTo parameter
                        $returnTo = $_GET['returnTo'] ?? $_POST['returnTo'] ?? '/dashboard';
                        $this->redirect($returnTo);
                    } else {
                        // Log failed login attempt
                        $this->addFailedLoginLog($username, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
                        $errorMessage = 'Invalid credentials';
                        if (!$ldapStatus['enabled']) {
                                $errorMessage .= '. Note: LDAP authentication is currently disabled.';
                        }
                        $data = [
                        'error' => $errorMessage,
                        'username' => $username,
                        'ldap_status' => $ldapStatus
                        ];
                        $this->view('auth/login', $data, false);
                    }
                }
            } catch (Exception $e) {
                error_log('Login error: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
                // Log failed login attempt due to error
                $this->addFailedLoginLog($username, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 'Authentication error: ' . $e->getMessage());
                // Provide more detailed error in development
                $errorMessage = 'Authentication service unavailable. Please try again later.';
                if (ini_get('display_errors')) {
                    $errorMessage .= ' (Error: ' . $e->getMessage() . ')';
                }

                $data = [
                    'error' => $errorMessage,
                    'username' => $username,
                    'ldap_status' => ['enabled' => false]
                ];
                $this->view('auth/login', $data, false);
            }
        } else {
            $this->view('auth/login', [], false);
        }
    }

    public function logout()
    {
        $username = $_SESSION['username'] ?? 'Unknown';
        $this->addLog("User logged out", "Username: " . $username);
        session_destroy();
        $this->redirect('/login');
    }

    private function addFailedLoginLog($username, $ipAddress, $reason = 'Invalid credentials')
    {
        $logModel = $this->model('LogModel');
        $logModel->addLog(null, $username ?: 'Unknown', 'Failed login attempt', $reason . ' | IP: ' . $ipAddress);
    }

    private function validateRegisterData($data, $isUpdate = false)
    {
        $errors = [];
        // Validate username
        if (empty($data['username'])) {
            $errors[] = "Username is required.";
        } elseif (strlen($data['username']) > 50) {
            $errors[] = "Username cannot exceed 50 characters.";
        }

        // Validate password (only if adding or if password is provided for update)
        if (!$isUpdate || (!empty($data['password']) && !$isUpdate)) {
            // Require password for add, optional for edit
            if (empty($data['password'])) {
                $errors[] = "Password is required.";
            } elseif (strlen($data['password']) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            } elseif (strlen($data['password']) > 255) {
                $errors[] = "Password cannot exceed 255 characters.";
            }
        }

        // Validate email
        if (empty($data['email'])) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } elseif (strlen($data['email']) > 255) {
            $errors[] = "Email cannot exceed 255 characters.";
        }

        // Validate full_name
        if (empty($data['full_name'])) {
            $errors[] = "Full name is required.";
        } elseif (strlen($data['full_name']) > 255) {
            $errors[] = "Full name cannot exceed 255 characters.";
        }

        // Validate role
        if (empty($data['role'])) {
            $errors[] = "Role is required.";
        } elseif (!in_array($data['role'], ['user', 'admin'])) {
            $errors[] = "Invalid role specified. Must be 'user' or 'admin'.";
        } elseif (strlen($data['role']) > 50) {
            $errors[] = "Role cannot exceed 50 characters.";
        }

        return $errors;
    }

    public function register()
    {
        $this->requireAuth();
        // Only admins can register new users
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'email' => trim($_POST['email'] ?? ''),
                'full_name' => trim($_POST['full_name'] ?? ''),
                'role' => $_POST['role'] ?? 'user' // Default to 'user' if not provided
            ];
            $errors = $this->validateRegisterData($data);
            if (!empty($errors)) {
                $data['error'] = implode(' ', $errors);
                $this->view('auth/register', $data);
                return;
            }

            $userModel = $this->model('UserModel');
            if ($userModel->create($data)) {
                $this->addLog("New user registered", "Username: " . $data['username']);
                $this->redirect('/users');
            } else {
                $data['error'] = 'Failed to create user. It is possible the username or email already exists.';
                $this->view('auth/register', $data);
            }
        } else {
            $this->view('auth/register');
        }
    }

    private function checkLDAPExtension()
    {
        if (!defined('LDAP_ENABLED') || !LDAP_ENABLED) {
            return [
                'enabled' => false,
                'message' => 'LDAP is not enabled or configured.',
                'instructions' => [] // Instructions might be in a view or config
            ];
        }

        if (!extension_loaded('ldap')) {
            return [
                'enabled' => false,
                'message' => 'LDAP extension for PHP is not loaded.',
                'instructions' => [
                    'windows_xampp' => [
                        '1. Open php.ini file (usually in C:\xampp\php\php.ini)',
                        '2. Find the line ;extension=ldap',
                        '3. Remove the semicolon to uncomment: extension=ldap',
                        '4. Save the file',
                        '5. Restart Apache from XAMPP Control Panel'
                    ],
                    'linux' => [
                        '1. Install php-ldap: sudo apt-get install php-ldap (Ubuntu/Debian) or sudo yum install php-ldap (CentOS/RHEL)',
                        '2. Restart Apache: sudo service apache2 restart'
                    ]
                ]
            ];
        }
        
        return ['enabled' => true];
    }
}
