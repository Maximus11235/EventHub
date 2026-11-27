<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

use EventHub\Database;
use EventHub\Http\JsonResponse;
use EventHub\Repositories\OrderRepository;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method !== 'POST') {
        JsonResponse::error('METHOD_NOT_ALLOWED', 'Use POST para finalizar compras.', 405);
    }

    $pdo  = Database::getConnection();
    $repo = new OrderRepository($pdo);
    $body = JsonResponse::readJsonBody();

    $clienteId = (int) ($_SESSION['user_id'] ?? ($body['cliente_id'] ?? 0));
    if ($clienteId <= 0) {
        JsonResponse::error('UNAUTHENTICATED', 'Faça login como cliente para concluir a compra.', 401);
    }

    $metodo = (string) ($body['metodo_pagamento'] ?? 'PIX');
    $items  = $body['items'] ?? [];

    $result = $repo->checkout($clienteId, $metodo, $items);
    JsonResponse::send(['data' => $result], 201);
} catch (DomainException $e) {
    JsonResponse::error('CHECKOUT_ERROR', $e->getMessage(), $e->getCode() ?: 400);
} catch (RuntimeException $e) {
    JsonResponse::error('DB_UNAVAILABLE', $e->getMessage(), 503);
}
