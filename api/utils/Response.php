<?php

class Response
{
    public static function setHeaders(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, X-User-Role, Authorization');
    }

    public static function json(int $statusCode, $payload): void
    {
        self::setHeaders();
        http_response_code($statusCode);
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error(int $statusCode, string $errorCode, string $message, array $details = []): void
    {
        $body = [
            'status' => $statusCode,
            'error_code' => $errorCode,
            'message' => $message
        ];

        if (!empty($details)) {
            $body['details'] = $details;
        }

        self::json($statusCode, $body);
    }
}
