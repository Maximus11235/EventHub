<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

use EventHub\Database;
use EventHub\Http\JsonResponse;
use EventHub\Repositories\TicketValidationRepository;

try {
    $pdo  = Database::getConnection();
    $repo = new TicketValidationRepository($pdo);

    $clienteId = (int) ($_SESSION['user_id'] ?? ($_GET['cliente_id'] ?? 0));
    if ($clienteId <= 0) {
        JsonResponse::error('UNAUTHENTICATED', 'Usuário não autenticado.', 401);
    }

    $result = $repo->listByClient($clienteId);
    JsonResponse::send($result);
} catch (RuntimeException $e) {
    JsonResponse::error('DB_UNAVAILABLE', $e->getMessage(), 503);
}
