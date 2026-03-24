<?php

namespace EduPay;

/**
 * Centralised authentication and authorisation helpers.
 *
 * Usage:
 *   Auth::requireLogin();           // redirect to login if no session
 *   Auth::requireRole('admin');     // 403 if logged-in user lacks the role
 */
class Auth
{
    /**
     * Ensure a user session is active.
     * Redirects to the login page if no session is found.
     *
     * @param string $loginPage Relative path to the login page.
     * @return void
     */
    public static function requireLogin(string $loginPage = 'login.php'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {
            header('Location: ' . $loginPage);
            exit();
        }
    }

    /**
     * Ensure the logged-in user holds the given role.
     * Must be called after requireLogin().
     *
     * @param string $role    Expected role string (e.g. 'admin').
     * @param int    $code    HTTP status code returned on failure.
     * @param string $message Message shown when access is denied.
     * @return void
     */
    public static function requireRole(string $role, int $code = 403, string $message = 'Access denied.'): void
    {
        if (($_SESSION['role'] ?? '') !== $role) {
            http_response_code($code);
            echo h($message);
            exit();
        }
    }

    /**
     * Return the currently authenticated user's session data, or null.
     *
     * @return array<string,mixed>|null
     */
    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        return [
            'id'             => (int) $_SESSION['user_id'],
            'name'           => (string) ($_SESSION['user_name'] ?? ''),
            'role'           => (string) ($_SESSION['role'] ?? ''),
            'institution_id' => isset($_SESSION['institution_id']) ? (int) $_SESSION['institution_id'] : null,
        ];
    }
}
