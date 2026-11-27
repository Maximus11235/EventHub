<?php

declare(strict_types=1);

namespace EventHub\Http;

final class JsonResponse
{
    public static function send(mixed $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function error(string $code, string $message, int $status = 400, array $details = []): never
    {
        $body = [
            'error'   => $code,
            'message' => $message,
        ];
        if (!empty($details)) {
            $body['details'] = $details;
        }
        self::send($body, $status);
    }

    public static function readJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            self::error('INVALID_JSON', 'Corpo da requisição JSON inválido.', 400);
        }
        return $decoded;
    }
}
