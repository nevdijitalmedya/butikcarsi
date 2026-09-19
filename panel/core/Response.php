<?php
/**
 * Response.php — JSON API response helpers
 */

class Response {

    public static function json(mixed $data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'OK'): never {
        self::json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error(string $message, int $code = 400, mixed $errors = null): never {
        self::json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    public static function paginated(array $items, int $total, int $page, int $perPage): never {
        self::json([
            'status' => 'success',
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]
        ]);
    }

    public static function handleOptions(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            http_response_code(200);
            exit;
        }
    }
}
