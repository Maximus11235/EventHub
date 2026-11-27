<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

use EventHub\Database;
use EventHub\Http\JsonResponse;
use EventHub\Repositories\UserRepository;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo  = Database::getConnection();
    $repo = new UserRepository($pdo);
    $body = JsonResponse::readJsonBody();
    $userId = (int) ($_SESSION['user_id'] ?? ($body['id'] ?? $_GET['id'] ?? 0));

    if ($userId <= 0) {
        JsonResponse::error('UNAUTHENTICATED', 'Usuário não autenticado.', 401);
    }

    if ($method === 'GET') {
        $user = $repo->findById($userId);
        if (!$user) {
            JsonResponse::error('NOT_FOUND', 'Usuário não encontrado.', 404);
        }
        JsonResponse::send(['user' => $user]);
    }

    if ($method === 'PUT') {
        $scope = $body['scope'] ?? 'profile';
        if ($scope === 'card') {
            $user = $repo->updateCard($userId, $body['cartao'] ?? null);
            JsonResponse::send(['user' => $user]);
        }

        $user = $repo->updateProfile($userId, $body);
        JsonResponse::send(['user' => $user]);
    }

    JsonResponse::error('METHOD_NOT_ALLOWED', 'Método HTTP não suportado.', 405);
} catch (DomainException $e) {
    JsonResponse::error('DOMAIN_ERROR', $e->getMessage(), $e->getCode() ?: 400);
} catch (RuntimeException $e) {
    JsonResponse::error('DB_UNAVAILABLE', $e->getMessage(), 503);
}
