<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

use EventHub\Database;
use EventHub\Http\JsonResponse;
use EventHub\Repositories\EventRepository;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo  = Database::getConnection();
    $repo = new EventRepository($pdo);

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $event = $repo->findById((int) $_GET['id']);
            if (!$event) {
                JsonResponse::error('NOT_FOUND', 'Evento não encontrado.', 404);
            }
            JsonResponse::send(['data' => $event]);
        }

        if (isset($_GET['anunciante_id'])) {
            $events = $repo->listByAdvertiser((int) $_GET['anunciante_id']);
            JsonResponse::send(['data' => $events]);
        }

        $search = isset($_GET['q']) ? (string) $_GET['q'] : null;
        JsonResponse::send(['data' => $repo->listActive($search)]);
    }

    if ($method === 'POST') {
        $body = JsonResponse::readJsonBody();
        $anuncianteId = (int) ($_SESSION['user_id'] ?? ($body['anunciante_id'] ?? 0));
        if ($anuncianteId <= 0) {
            JsonResponse::error('FORBIDDEN', 'Apenas anunciantes autenticados podem publicar eventos.', 403);
        }

        $action = (string) ($body['action'] ?? 'create');
        if ($action === 'assign_validator') {
            $eventId = (int) ($body['event_id'] ?? 0);
            $identificador = (string) ($body['validador'] ?? '');
            $updated = $repo->assignValidator($eventId, $anuncianteId, $identificador);
            JsonResponse::send(['data' => $updated]);
        }

        $created = $repo->create($anuncianteId, $body);
        JsonResponse::send(['data' => $created], 201);
    }

    if ($method === 'DELETE') {
        $eventId = (int) ($_GET['id'] ?? 0);
        $anuncianteId = (int) ($_SESSION['user_id'] ?? ($_GET['anunciante_id'] ?? 0));
        if ($eventId <= 0 || $anuncianteId <= 0) {
            JsonResponse::error('BAD_REQUEST', 'Parâmetros id e anunciante_id são obrigatórios.', 400);
        }

        if (isset($_GET['validador_id'])) {
            $updated = $repo->removeValidator($eventId, $anuncianteId, (int) $_GET['validador_id']);
            JsonResponse::send(['data' => $updated]);
        }

        $repo->delete($eventId, $anuncianteId);
        JsonResponse::send(['deleted' => true]);
    }

    JsonResponse::error('METHOD_NOT_ALLOWED', 'Método HTTP não suportado.', 405);
} catch (DomainException $e) {
    JsonResponse::error('DOMAIN_ERROR', $e->getMessage(), $e->getCode() ?: 400);
} catch (RuntimeException $e) {
    JsonResponse::error('DB_UNAVAILABLE', $e->getMessage(), 503);
}
