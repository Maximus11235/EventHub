<?php

declare(strict_types=1);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$fullPath = __DIR__ . $uri;

if ($uri !== '/' && is_file($fullPath)) {
    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
    if ($ext !== 'php') {
        return false;
    }
    require $fullPath;
    return true;
}

if ($uri === '/' || $uri === '') {
    require __DIR__ . '/index.php';
    return true;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'NOT_FOUND', 'message' => 'Rota não encontrada.']);
return true;
