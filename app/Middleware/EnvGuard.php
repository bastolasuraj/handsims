<?php

// app/middleware/EnvGuard.php
// Environment and Authentication Guards

namespace App\Middleware;

class EnvGuard
{
    /**
     * Require non-production environment
     * Blocks access in production
     */
    public static function requireNonProd(): void
    {
        if (getenv('APP_ENV') === 'production' || getenv('ENVIRONMENT') === 'production') {
            http_response_code(404);
            exit();
        }
    }

    /**
     * Require admin authentication
     */
    public static function requireAdmin(): void
    {
        session_start();
        if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
            http_response_code(403);
            exit('Unauthorized');
        }
    }

    /**
     * Require authenticated user
     */
    public static function requireAuth(): void
    {
        session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit();
        }
    }

    /**
     * Generate a signed URL with expiration
     * @param string $path The path to sign
     * @param int $ttl Time to live in seconds (default 5 minutes)
     * @return string The signed URL
     */
    public static function signUrl(string $path, int $ttl = 300): string
    {
        $secret = getenv('URL_SIGNING_SECRET') ?: 'default-secret-change-in-production';
        $exp = time() + $ttl;
        $sig = hash_hmac('sha256', $path . $exp, $secret);
        return $path . '?exp=' . $exp . '&sig=' . $sig;
    }

    /**
     * Verify a signed URL
     * @param string $path The path to verify
     * @param int $exp The expiration timestamp
     * @param string $sig The signature to verify
     * @return bool True if valid, false otherwise
     */
    public static function verifySignature(string $path, int $exp, string $sig): bool
    {
        if (time() > $exp) {
            return false;
        }
        $secret = getenv('URL_SIGNING_SECRET') ?: 'default-secret-change-in-production';
        $calc = hash_hmac('sha256', $path . $exp, $secret);
        return hash_equals($calc, $sig);
    }
}
