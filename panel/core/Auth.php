<?php
/**
 * Auth.php — Session-based authentication for both Admin and Producer panels
 */

class Auth {

    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Admin login
     */
    public static function adminLogin(string $username, string $password): bool {
        self::initSession();

        $user = Database::query(
            "SELECT * FROM admin_users WHERE username = ? AND is_active = 1",
            [$username]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            Database::execute("UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = ?", [$user['id']]);

            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_full_name'] = $user['full_name'];
            $_SESSION['panel_type'] = 'admin';

            return true;
        }
        return false;
    }

    /**
     * Producer login
     */
    public static function producerLogin(string $email, string $password): bool {
        self::initSession();

        $producer = Database::query(
            "SELECT * FROM producers WHERE email = ? AND status IN ('approved', 'pending')",
            [$email]
        );

        if ($producer && password_verify($password, $producer['password_hash'])) {
            Database::execute("UPDATE producers SET last_login = CURRENT_TIMESTAMP WHERE id = ?", [$producer['id']]);

            $_SESSION['producer_id'] = $producer['id'];
            $_SESSION['producer_email'] = $producer['email'];
            $_SESSION['producer_brand'] = $producer['brand_name'];
            $_SESSION['producer_slug'] = $producer['slug'];
            $_SESSION['producer_status'] = $producer['status'];
            $_SESSION['panel_type'] = 'producer';

            return true;
        }
        return false;
    }

    /**
     * Enforce admin authentication
     */
    public static function requireAdmin(): void {
        self::initSession();
        if (empty($_SESSION['admin_id']) || ($_SESSION['panel_type'] ?? '') !== 'admin') {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Enforce producer authentication
     */
    public static function requireProducer(): void {
        self::initSession();
        if (empty($_SESSION['producer_id']) || ($_SESSION['panel_type'] ?? '') !== 'producer') {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Get admin user ID
     */
    public static function adminId(): int {
        self::initSession();
        return $_SESSION['admin_id'] ?? 0;
    }

    /**
     * Get producer ID
     */
    public static function producerId(): int {
        self::initSession();
        return $_SESSION['producer_id'] ?? 0;
    }

    /**
     * Check if user is superadmin
     */
    public static function isSuperAdmin(): bool {
        self::initSession();
        return ($_SESSION['admin_role'] ?? '') === 'superadmin';
    }

    /**
     * Get current panel type
     */
    public static function panelType(): string {
        self::initSession();
        return $_SESSION['panel_type'] ?? '';
    }

    /**
     * Logout
     */
    public static function logout(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
