<?php

// app/Helpers/csrf.php
// CSRF Protection Helper Functions

/**
 * Generate or retrieve the current CSRF token
 * @return string The CSRF token for the current session
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/**
 * Verify a CSRF token against the session token
 * @param string $token The token to verify
 * @return bool True if valid, false otherwise
 */
function csrf_verify(string $token): bool
{
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

/**
 * Generate a hidden input field with CSRF token
 * @return string HTML for hidden CSRF input
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

/**
 * Verify CSRF for POST requests and exit if invalid
 * Call this at the beginning of any POST handler
 */
function csrf_check(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify($_POST['csrf'] ?? '')) {
            http_response_code(419);
            exit('CSRF validation failed');
        }
    }
}
