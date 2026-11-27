<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

use EventHub\Database;
use EventHub\Http\JsonResponse;
use EventHub\Repositories\TicketValidationRepository;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo  = Database::getConnection();
    $repo = new TicketValidationRepository($pdo);
    $validadorId = (int) ($_SESSION['user_id'] ?? ($_GET['validador_id'] ?? 0));

    if ($method === 'GET') {
        if (isset($_GET['list'])) {
            $filterId = $validadorId > 0 ? $validadorId : null;
            JsonResponse::send(['data' => $repo->listRecentForPortaria($filterId)]);
        }

        $qr = (string) ($_GET['qr'] ?? '');
        if (trim($qr) === '') {
            JsonResponse::error('BAD_REQUEST', 'Informe o parâmetro qr.', 400);
        }

        $filterId = $validadorId > 0 ? $validadorId : null;
        JsonResponse::send($repo->inspectQrCode($qr, $filterId));
    }

    if ($method === 'POST') {
        $body = JsonResponse::readJsonBody();
        $qr = (string) ($body['codigo_qr'] ?? '');
        $postValidadorId = (int) ($_SESSION['user_id'] ?? ($body['validador_id'] ?? 0));

        if ($postValidadorId <= 0) {
            JsonResponse::error('FORBIDDEN', 'Validador não autenticado.', 403);
        }

        $result = $repo->confirmCheckIn($qr, $postValidadorId);
        JsonResponse::send($result);
    }

    JsonResponse::error('METHOD_NOT_ALLOWED', 'Método HTTP não suportado.', 405);
} catch (DomainException $e) {
    JsonResponse::error('VALIDATION_ERROR', $e->getMessage(), $e->getCode() ?: 400);
} catch (RuntimeException $e) {
    JsonResponse::error('DB_UNAVAILABLE', $e->getMessage(), 503);
}
